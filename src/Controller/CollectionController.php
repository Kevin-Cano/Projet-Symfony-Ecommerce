<?php

namespace App\Controller;

use App\Repository\WatchRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Watch;

class CollectionController extends AbstractController
{
    #[Route('/collection', name: 'app_collection')]
    public function index(EntityManagerInterface $entityManager, Request $request, PaginatorInterface $paginator): Response
    {
        $watchRepository = $entityManager->getRepository(Watch::class);
        
        // Récupérer le filtre de prix
        $priceFilter = $request->query->get('price', 'all');
        // Récupérer le filtre de bracelet
        $materialFilter = $request->query->get('material', 'all');
        
        // Obtenir la requête filtrée (pas encore exécutée)
        $query = $this->getFilteredWatchesQuery($watchRepository, $priceFilter, $materialFilter);
        
        // Paginer les résultats
        $pagination = $paginator->paginate(
            $query, // Requête
            $request->query->getInt('page', 1), // Page
            9 // Limite par page
        );
        
        return $this->render('collection/index.html.twig', [
            'pagination' => $pagination,
            'priceFilter' => $priceFilter,
            'materialFilter' => $materialFilter
        ]);
    }

    /**
     * Retourne la requête filtrée par prix et matériau
     */
    private function getFilteredWatchesQuery($repository, string $priceFilter, string $materialFilter)
    {
        // Créer le QueryBuilder de base
        $queryBuilder = $repository->createQueryBuilder('w')
            ->andWhere('w.author IS NULL');
        
        // Appliquer le filtre de prix
        if ($priceFilter !== 'all') {
            switch ($priceFilter) {
                case 'less1000':
                    $queryBuilder->andWhere('w.price >= :minPrice')
                        ->andWhere('w.price <= :maxPrice')
                        ->setParameter('minPrice', 0)
                        ->setParameter('maxPrice', 1000);
                    break;
                case '1000to5000':
                    $queryBuilder->andWhere('w.price >= :minPrice')
                        ->andWhere('w.price <= :maxPrice')
                        ->setParameter('minPrice', 1000)
                        ->setParameter('maxPrice', 5000);
                    break;
                case '5000to10000':
                    $queryBuilder->andWhere('w.price >= :minPrice')
                        ->andWhere('w.price <= :maxPrice')
                        ->setParameter('minPrice', 5000)
                        ->setParameter('maxPrice', 10000);
                    break;
                case 'more10000':
                    $queryBuilder->andWhere('w.price >= :minPrice')
                        ->setParameter('minPrice', 10000);
                    break;
            }
        }
        
        // Appliquer le filtre de bracelet
        if ($materialFilter !== 'all') {
            $queryBuilder->andWhere('w.material = :material')
                ->setParameter('material', $materialFilter);
        }
        
        return $queryBuilder->getQuery();
    }
}
