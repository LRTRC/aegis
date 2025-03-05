<?php

namespace App\Controller\Admin;

use App\Entity\Event;
use App\Entity\Participant;
use App\Entity\Professional;
use App\Entity\Session;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // Statistiques générales
        $stats = [
            'participants' => $this->entityManager->getRepository(Participant::class)->count([]),
            'professionals' => $this->entityManager->getRepository(Professional::class)->count([]),
            'events' => $this->entityManager->getRepository(Event::class)->count([]),
            'sessions' => $this->entityManager->getRepository(Session::class)->count([]),
        ];

        // Derniers événements créés
        $latestEvents = $this->entityManager->getRepository(Event::class)
            ->createQueryBuilder('e')
            ->orderBy('e.id', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        // Dernières sessions
        $latestSessions = $this->entityManager->getRepository(Session::class)
            ->createQueryBuilder('s')
            ->orderBy('s.id', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        // Stats pour le graphique des types d'événements
        $eventTypeStats = $this->entityManager->getRepository(Event::class)
            ->createQueryBuilder('e')
            ->select('e.type as type, COUNT(e.id) as count')
            ->groupBy('e.type')
            ->getQuery()
            ->getResult();

        $eventStats = array_map(function ($stat) {
            return [
                'value' => $stat['count'],
                'label' => (new Event())->getTypeLabel($stat['type'])
            ];
        }, $eventTypeStats);

        // Stats pour le graphique des sessions mensuelles
        $sessionStats = $this->entityManager->getRepository(Session::class)
            ->createQueryBuilder('s')
            ->select('s.dateDebut', 'COUNT(s.id) as count')
            ->groupBy('s.dateDebut')
            ->getQuery()
            ->getResult();

        $monthlyStats = array_fill(0, 12, 0); // Initialize array with zeros
        foreach ($sessionStats as $stat) {
            $month = (int)$stat['dateDebut']->format('n'); // 1 à 12
            $monthlyStats[$month - 1] = $stat['count'];
        }

        return $this->render('admin/dashboard.html.twig', [
            'stats' => $stats,
            'latest_events' => $latestEvents,
            'latest_sessions' => $latestSessions,
            'event_stats' => array_column($eventStats, 'value'),
            'event_labels' => array_column($eventStats, 'label'),
            'session_stats' => $monthlyStats,
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Aegis Admin')
            ->setFaviconPath('favicon.svg')
            ->renderContentMaximized()
            ->setLocales(['fr'])
            ->generateRelativeUrls();
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToRoute('Retour au site', 'fa fa-home', 'app_home');
        yield MenuItem::section('');

        yield MenuItem::section('Utilisateurs');
        yield MenuItem::subMenu('Gestion utilisateurs', 'fas fa-users')->setSubItems([
            MenuItem::linkToCrud('Participants', 'fas fa-user', Participant::class),
            MenuItem::linkToCrud('Professionnels', 'fas fa-user-tie', Professional::class),
        ]);

        yield MenuItem::section('Événements');
        yield MenuItem::linkToCrud('Événements', 'fas fa-calendar-alt', Event::class);
        yield MenuItem::linkToCrud('Sessions', 'fas fa-clock', Session::class);
    }
}
