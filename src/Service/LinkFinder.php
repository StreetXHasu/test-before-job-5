<?php

namespace App\Service;

use App\Entity\Link;
use App\Repository\LinkRepository;

class LinkFinder
{
    private LinkRepository $linkRepository;

    public function __construct(LinkRepository $linkRepository)
    {
        $this->linkRepository = $linkRepository;
    }

    public function findByCode(string $code): ?Link
    {
        $link = $this->linkRepository->findOneBy(['shortCode' => $code]);
        if (!$link) {
            $link = $this->linkRepository->findOneBy(['customCode' => $code]);
        }
        return $link;
    }
} 