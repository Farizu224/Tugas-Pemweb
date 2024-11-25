<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar with Slide Effect</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Toggle Button -->
    <button id="sidebarToggle" class="btn btn-primary m-3"><i class="fas fa-bars"></i></button>

    <!-- Sidebar -->
    <div id="sidebar" class="bg-dark text-white">
        <button id="closeSidebar" class="btn btn-light mt-3"><i class="fas fa-times"></i></button>
        <h3 class="text-center py-3">Sidebar Title</h3>
        <ul class="list-unstyled components">
            <li class="active">
                <a href="#homeSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle text-white">
                    Home
                </a>
                <ul class="collapse list-unstyled" id="homeSubmenu">
                    <li><a href="#" class="text-white">Home 1</a></li>
                    <li><a href="#" class="text-white">Home 2</a></li>
                    <li><a href="#" class="text-white">Home 3</a></li>
                </ul>
            </li>
            <li><a href="#" class="text-white">About</a></li>
            <li><a href="#" class="text-white">Portfolio</a></li>
            <li><a href="#" class="text-white">Contact</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="container-fluid p-4">
        <h2>Main Content</h2>
        <p>Insert your main content here.</p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>
