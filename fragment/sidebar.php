<?php
// Vérifier session et déterminer le rôle
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$role_name = null;


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$role_name = null;

// priorité للـ admin / technicien
if (isset($_SESSION['tech_role']) && in_array($_SESSION['tech_role'], ['admin', 'technicien'])) {
    $role_name = $_SESSION['tech_role'];
}
// sinon user
elseif (isset($_SESSION['usr_type']) && $_SESSION['usr_type'] === 'user') {
    $role_name = 'user';
}
?>


<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Gestion Tickets</div>
    </a>

    <hr class="sidebar-divider my-0">

    <!-- Dashboard -->
    <li class="nav-item">
        <a class="nav-link" href="index.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <div class="sidebar-heading">Tickets</div>

    <?php if ($role_name === 'user'): ?>
        <!-- Sidebar pour utilisateur -->
        <li class="nav-item">
            <a class="nav-link" href="cree_ticket.php">
                <i class="fas fa-plus"></i>
                <span>Créer un ticket</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="mes_tickets.php">
                <i class="fas fa-list"></i>
                <span>Mes tickets</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="ticket_en_cours.php">
                <i class="fas fa-hourglass-half"></i>
                <span>Tickets en attente</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="ticket_resolu.php">
                <i class="fas fa-check"></i>
                <span>Tickets résolus</span>
            </a>
        </li>

    <?php elseif ($role_name === 'technicien'): ?>
        <!-- Sidebar pour technicien -->
        <li class="nav-item">
            <a class="nav-link" href="tickets_assignes.php">
                <i class="fas fa-tasks"></i>
                <span>Tickets assignés</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="tickets_tous.php">
                <i class="fas fa-list"></i>
                <span>Tous les tickets</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="interventions.php">
                <i class="fas fa-tools"></i>
                <span>Interventions</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="cree_intervention.php">
                <i class="fas fa-plus"></i>
                <span>Créer intervention</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="materiel.php">
                <i class="fas fa-fw fa-laptop"></i>
                <span>Matériel</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="ajouter_materiel.php">
                <i class="fas fa-plus"></i>
                <span>Ajouter Matériel</span>
            </a>
        </li>


    <?php elseif ($role_name === 'admin'): ?>
        <!-- Sidebar pour admin -->
        <li class="nav-item">
            <a class="nav-link" href="tickets_tous.php">
                <i class="fas fa-list"></i>
                <span>Tous les tickets</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="tickets_assignes.php">
                <i class="fas fa-tasks"></i>
                <span>Tickets assignés</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="interventions.php">
                <i class="fas fa-tools"></i>
                <span>Interventions</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="cree_intervention.php">
                <i class="fas fa-plus"></i>
                <span>Créer intervention</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="materiel.php">
                <i class="fas fa-fw fa-laptop"></i>
                <span>Matériel</span>
            </a>
        </li>
         <li class="nav-item">
            <a class="nav-link" href="ajouter_materiel.php">
                <i class="fas fa-plus"></i>
                <span>Ajouter Matériel</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="bc.php">
                <i class="fas fa-fw fa-book"></i>
                <span>Base de connaissance</span>
            </a>
        </li>
         <li class="nav-item">
            <a class="nav-link" href="gestion_user.php">
                <i class="fas fa-users"></i>
                <span>Gestion utilisateurs</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="inscription.php">
                <i class="fas fa-users"></i>
                <span>ajouter utilisateurs</span>
            </a>
        </li>
    <?php endif; ?>

    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>

<!-- Scripts SB Admin -->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>
