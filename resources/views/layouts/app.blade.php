<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Rutas UAGRM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
        }
        .sidebar .nav-link {
            color: #fff;
        }
        .sidebar .nav-link:hover {
            background-color: #495057;
        }
        .main-content {
            padding: 20px;
        }
        .map-container {
            position: relative;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            overflow: hidden;
        }
        .map-image {
            width: 100%;
            height: auto;
        }
        .node {
            position: absolute;
            width: 20px;
            height: 20px;
            background-color: red;
            border-radius: 50%;
            transform: translate(-50%, -50%);
            cursor: pointer;
        }
        .node:hover {
            background-color: #dc3545;
        }
        .node.selected {
            background-color: #28a745;
            border: 2px solid #fff;
        }
        .edge {
            position: absolute;
            background-color: blue;
            transform-origin: 0 0;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <div class="d-flex flex-column p-3">
                    <h4 class="text-white text-center mb-4">
                        <i class="fas fa-map-marked-alt"></i> UAGRM Routes
                    </h4>
                    <ul class="nav nav-pills flex-column mb-auto">
                        <li class="nav-item">
                            <a href="{{ route('dashboard') }}" class="nav-link active">
                                <i class="fas fa-home me-2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('lugares.index') }}" class="nav-link">
                                <i class="fas fa-building me-2"></i> Lugares
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('caminos.index') }}" class="nav-link">
                                <i class="fas fa-road me-2"></i> Caminos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('rutas.calcular') }}" class="nav-link">
                                <i class="fas fa-route me-2"></i> Calcular Ruta
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @yield('scripts')
</body>
</html>