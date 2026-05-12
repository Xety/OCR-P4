<?php

declare(strict_types=1);

namespace App\Core;

final class Paginator
{
    /**
     * Page courante, à partir de 1. Si la page demandée est inférieure à 1, elle sera corrigée à 1.
     * @var int
     */
    private readonly int $currentPage;

    /**
     * Nombre total de pages.
     * @var int
     */
    private readonly int $totalPages;

    /**
     * Constructeur privé pour forcer l'utilisation de la méthode fromRequest.
     *
     * @param int $page Page courante demandée.
     * @param int $perPage Nombre d'items par page.
     * @param int $total Nombre total d'items.
     */
    public function __construct(
        int $page,
        private readonly int $perPage,
        private readonly int $total,
    ) {
        $this->totalPages  = $perPage > 0 && $total > 0 ? (int) ceil($total / $perPage) : 1;
        $this->currentPage = max(1, $page);
    }

    /**
     * Retourne la page courante.
     *
     * @return int
     */
    public function getCurrentPage(): int {
        return $this->currentPage;
    }

    /**
     * Retourne le nombre d'items par page.
     *
     * @return int
     */
    public function getPerPage(): int {
        return $this->perPage;
    }

    /**
     * Retourne le nombre total de pages.
     *
     * @return int
     */
    public function getTotalPages(): int  {
        return $this->totalPages;
    }

    /**
     * Retourne l'offset pour la requête SQL.
     *
     * @return int
     */
    public function getOffset(): int {
        return ($this->currentPage - 1) * $this->perPage;
    }

    /**
     * Indique s'il y a plusieurs pages.
     *
     * @return bool
     */
    public function hasPages(): bool {
        return $this->totalPages > 1;
    }

    /**
     * Indique s'il y a une page précédente.
     *
     * @return bool
     */
    public function hasPreviousPage(): bool {
        return $this->currentPage > 1;
    }

    /**
     * Indique s'il y a une page suivante.
     *
     * @return bool
     */
    public function hasNextPage(): bool   {
        return $this->currentPage < $this->totalPages;
    }

    /**
     * Indique si la page courante est hors limites.
     *
     * @return bool
     */
    public function isOutOfBounds(): bool {
        return $this->currentPage > $this->totalPages && $this->totalPages > 0;
    }
}