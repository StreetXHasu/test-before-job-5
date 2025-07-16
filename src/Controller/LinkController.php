<?php

namespace App\Controller;

use App\Entity\Link;
use App\Repository\LinkRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\LinkFinder;

class LinkController extends AbstractController
{
    #[Route('/api/links', name: 'link_list', methods: ['GET'])]
    public function list(LinkRepository $linkRepository): JsonResponse
    {
        $links = $linkRepository->findAll();
        $data = array_map(function (Link $link) {
            return [
                'id' => $link->getId(),
                'shortCode' => $link->getShortCode(),
                'customCode' => $link->getCustomCode(),
                'originalUrl' => $link->getOriginalUrl(),
            ];
        }, $links);
        return $this->json($data);
    }

    #[Route('/api/links', name: 'link_add', methods: ['POST'])]
    public function add(Request $request, EntityManagerInterface $em, LinkRepository $linkRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!isset($data['url']) || !filter_var($data['url'], FILTER_VALIDATE_URL)) {
            return $this->json(['error' => 'Invalid URL'], Response::HTTP_BAD_REQUEST);
        }
        $customCode = $data['customCode'] ?? null;
        $shortCode = $customCode ?? substr(md5(uniqid()), 0, 6);
        if ($linkRepository->findOneBy(['shortCode' => $shortCode]) || ($customCode && $linkRepository->findOneBy(['customCode' => $customCode]))) {
            return $this->json(['error' => 'Short code already exists'], Response::HTTP_CONFLICT);
        }
        $link = new Link();
        $link->setOriginalUrl($data['url']);
        $link->setShortCode($shortCode);
        $link->setCustomCode($customCode);
        $link->setCreatedAt(new \DateTimeImmutable());
        $em->persist($link);
        $em->flush();
        return $this->json([
            'id' => $link->getId(),
            'shortCode' => $link->getShortCode(),
            'customCode' => $link->getCustomCode(),
            'originalUrl' => $link->getOriginalUrl(),
        ], Response::HTTP_CREATED);
    }

    #[Route('/api/links/{shortCode}', name: 'link_go', methods: ['GET'])]
    public function go(string $shortCode, LinkFinder $linkFinder): Response
    {
        $link = $linkFinder->findByCode($shortCode);
        if (!$link) {
            return $this->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        }
        return $this->json([
            'id' => $link->getId(),
            'shortCode' => $link->getShortCode(),
            'customCode' => $link->getCustomCode(),
            'originalUrl' => $link->getOriginalUrl(),
        ]);
    }

    #[Route('/{shortCode}', name: 'link_redirect', methods: ['GET'])]
    public function redirectToOriginal(string $shortCode, LinkFinder $linkFinder): Response
    {
        $link = $linkFinder->findByCode($shortCode);
        if (!$link) {
            return new Response('Not found', Response::HTTP_NOT_FOUND);
        }
        return $this->redirect($link->getOriginalUrl());
    }
} 