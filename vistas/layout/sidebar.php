<?php
// sidebar.php

if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Iniciar sesión si no está iniciada
}

$user_role = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : '';
$user_type = isset($_SESSION['user_type']) ? $_SESSION['user_type'] : '';

// Función para verificar permisos
function hasAccess($required_roles = [], $required_types = [])
{
    global $user_role, $user_type;

    // Verificar roles y tipos de usuario
    $role_access = empty($required_roles) || in_array($user_role, $required_roles);
    $type_access = empty($required_types) || in_array($user_type, $required_types);

    return $role_access && $type_access;
}
?>
<aside class="left-sidebar">
    <div class="scroll-sidebar">
        <nav class="sidebar-nav">
            <ul id="sidebarnav">
                <!-- DASHBOARD -->
                <?php if (hasAccess(['superadmin', 'adminrh', 'adminpr', 'user', 'applicant', 'supplier'])): ?>
                    <li>
                        <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false">
                            <i class="mdi mdi-view-dashboard"></i>
                            <span class="hide-menu"><b>DASHBOARD</b></span>
                        </a>
                        <ul aria-expanded="false" class="collapse">
                            <?php if ($user_role == 'superadmin'): ?>
                                <li><a href="dashboardSuperadmin"><i class="mdi mdi-view-dashboard"></i> Dashboard Superadmin</a></li>
                            <?php endif; ?>
                            <?php if ($user_role == 'adminrh'): ?>
                                <li><a href="dashboardAdminRH"><i class="mdi mdi-human-male-female"></i> Dashboard Admin RH</a></li>
                            <?php endif; ?>
                            <?php if ($user_role == 'adminpr'): ?>
                                <li><a href="dashboardAdminPR"><i class="mdi mdi-truck"></i> Dashboard Admin PR</a></li>
                            <?php endif; ?>
                            <?php if ($user_role == 'user'): ?>
                                <li><a href="dashboardUser"><i class="mdi mdi-account"></i> Dashboard Usuario</a></li>
                            <?php endif; ?>
                            <?php if ($user_role == 'applicant'): ?>
                                <li><a href="dashboardUser"><i class="mdi mdi-account"></i> Dashboard Postulante</a></li>
                            <?php endif; ?>
                            <?php if ($user_role == 'supplier'): ?>
                                <li><a href="dashboardUser"><i class="mdi mdi-account"></i> Dashboard Proveedor</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>

                <li class="nav-devider"></li>

                <!-- CONFIGURACIÓN -->

                <!-- CONFIGURACIÓN -->
                <?php if (hasAccess(['superadmin', 'adminrh', 'adminpr'])): ?>
                    <li>
                        <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false">
                            <i class="mdi mdi-settings"></i>
                            <span class="hide-menu"><b>CONFIGURACIÓN</b></span>
                        </a>
                        <ul aria-expanded="false" class="collapse">

                            <?php if (hasAccess(['superadmin', 'adminrh'])): ?>
                                <li><a href="administrar"><i class="mdi mdi-store"></i> Crear Empresa, Áreas y Puestos</a></li>
                                <li><a href="documentName"><i class="mdi mdi-store"></i> Crear de Documentos Usuarios</a></li>
                                <li><a href="documentMandatory"><i class="fa fa-book"></i> Asignar Documentos a Puestos</a></li>

                            <?php endif; ?>

                            <?php if (hasAccess(['superadmin', 'adminpr'])): ?>
                                <li><a href="documentNameSupplier"><i class="mdi mdi-file-document"></i> Crear Documentos Proveedores</a></li>
                            <?php endif; ?>

                        </ul>
                    </li>
                <?php endif; ?>

                <li class="nav-devider"></li>
                <?php if (hasAccess(['superadmin', 'adminrh', 'adminpr'])): ?>
                    <li>
                        <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false">
                            <i class="mdi mdi-settings"></i>
                            <span class="hide-menu"><b>GESTIÓN</b></span>
                        </a>
                        <ul aria-expanded="false" class="collapse">

                            <?php if (hasAccess(['superadmin', 'adminrh'])): ?>
                                <li><a href="user"><i class="fa fa-user-circle"></i> Usuarios</a></li>
                                <li><a href="applicants"><i class="fa fa-users"></i> Postulantes</a></li>
                            <?php endif; ?>

                            <?php if (hasAccess(['superadmin', 'adminpr'])): ?>
                                <li><a href="supplier"><i class="fa fa-truck"></i> Proveedores</a></li>
                            <?php endif; ?>

                        </ul>
                    </li>
                <?php endif; ?>

                <li class="nav-devider"></li>

                <?php if (hasAccess(['superadmin', 'adminrh', 'adminpr'])): ?>
                    <li>
                        <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false">
                            <i class="mdi mdi-settings"></i>
                            <span class="hide-menu"><b>EVALUACIÓN</b></span>
                        </a>
                        <ul aria-expanded="false" class="collapse">

                            <?php if (hasAccess(['superadmin', 'adminrh'])): ?>
                                <li><a href="admin_user_documents"><i class="mdi mdi-store"></i> Colaboradores</a></li>
                                <li><a href="admin_applicant_documents"><i class="fa fa-users"></i> Postulantes</a></li>
                            <?php endif; ?>

                            <?php if (hasAccess(['superadmin', 'adminpr'])): ?>
                                <li><a href="proveedores"><i class="mdi mdi-truck"></i> Empresas</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>


                <li class="nav-devider"></li>

                <?php if (hasAccess(['superadmin', 'adminrh', 'adminpr','applicant','supplier'])): ?>
                    <li>
                        <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false">
                            <i class="mdi mdi-settings"></i>
                            <span class="hide-menu"><b>DOCUMENTACIÓN</b></span>
                        </a>
                        <ul aria-expanded="false" class="collapse">

                            <?php if (hasAccess(['superadmin', 'adminrh','adminpr','user'])): ?>
                                <li><a href="documentUpload"><i class="mdi mdi-store"></i> Subir Documentos</a></li>
   
                            <?php endif; ?>

                            <?php if (hasAccess(['applicant'])): ?>
                                <li><a href="documentApplicant"><i class="mdi mdi-truck"></i> Subir Documentos</a></li>
                                <li><a href="experience"><i class="mdi mdi-truck"></i> Experiencia</a></li>


                            <?php endif; ?>

                            <?php if (hasAccess(['supplier'])): ?>
                                <li><a href="documentSupplier"><i class="mdi mdi-truck"></i> Subir Documentos</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>




                <!-- USUARIOS -->
                <?php if (hasAccess(['superadmin', 'adminrh', 'adminpr'])): ?>
                    <li>
                        <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false">
                            <i class="mdi mdi-account-multiple"></i>
                            <span class="hide-menu"><b>USUARIOS</b></span>
                        </a>
                        <ul aria-expanded="false" class="collapse">
                            <?php if (hasAccess(['superadmin', 'adminrh'])): ?>
                                <li><a href="registrar_usuario"><i class="mdi mdi-account-plus"></i> Registrar Usuario</a></li>
                                <li><a href="actualizar_usuario"><i class="mdi mdi-account-edit"></i> Actualizar Usuario</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>


                <li class="nav-devider"></li>
                <!-- OTROS MENÚS -->
                <?php if ($user_type == 'applicant'): ?>
                    <li>
                        <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false">
                            <i class="mdi mdi-account-box"></i>
                            <span class="hide-menu"><b>POSTULANTES</b></span>
                        </a>
                        <ul aria-expanded="false" class="collapse">
                            <li><a href="dashboardApplicant"><i class="mdi mdi-view-dashboard"></i> Dashboard</a></li>
                            <li><a href="applicant_details"><i class="mdi mdi-account-details"></i> Detalles del Postulante</a></li>
                            <li><a href="experience"><i class="mdi mdi-file"></i> Documentos del Postulante</a></li>
                            <li><a href="mostrar_experiencia"><i class="mdi mdi-file"></i> Documentos del Postulante</a></li>
                            <li><a href="edit_experience"><i class="mdi mdi-file"></i> Documentos del Postulante</a></li>
                        </ul>
                    </li>


                    <li class="nav-devider"></li>
                <?php elseif ($user_type == 'supplier'): ?>
                    <li>
                        <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false">
                            <i class="mdi mdi-truck-delivery"></i>
                            <span class="hide-menu"><b>PROVEEDORES</b></span>
                        </a>
                        <ul aria-expanded="false" class="collapse">
                            <li><a href="supplier_dashboard"><i class="mdi mdi-view-dashboard"></i> Dashboard Proveedor</a></li>
                            <li><a href="supplierDetails"><i class="mdi mdi-account-box"></i> Detalles del Proveedor</a></li>
                        </ul>
                    </li>


                    <li class="nav-devider"></li>
                <?php elseif ($user_type == 'user'): ?>
                    <li>
                        <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false">
                            <i class="mdi mdi-truck-delivery"></i>
                            <span class="hide-menu"><b> DCOUMENTACIÓN USUARIOS</b></span>
                        </a>
                        <ul aria-expanded="false" class="collapse">
                            <li><a href="supplier_dashboard"><i class="mdi mdi-view-dashboard"></i> Dashboard Proveedor</a></li>
                            <li><a href="documentUpload"><i class="mdi mdi-account-box"></i> Subir Documentos</a></li>
                            <li><a href="user_details"><i class="mdi mdi-account-box"></i>Registrar Datos</a></li>
                        </ul>
                    </li>
                <?php endif; ?>

                <!-- CERRAR SESIÓN -->
                <li class="nav-devider"></li>
                <li>
                    <a href="logout" class="waves-effect waves-dark">
                        <i class="mdi mdi-logout"></i>
                        <span class="hide-menu"><b>Cerrar Sesión</b></span>
                    </a>
                </li>
                <li class="nav-devider"></li>
            </ul>
        </nav>
    </div>
</aside>

<div class="page-wrapper">
    <!-- ============================================================== -->
    <!-- Container fluid  -->
    <div class="scalable-container">
        <!-- ============================================================== -->
        <div class="container-fluid">
            <!-- ============================================================== -->
            <!-- Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->

            <div class="">
                <button class="right-side-toggle waves-effect waves-light btn-inverse btn btn-circle btn-sm pull-right m-l-10">
                    <i class="ti-settings text-white"></i>
                </button>
            </div>