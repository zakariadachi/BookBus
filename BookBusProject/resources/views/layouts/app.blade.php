<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SATAS - Voyagez au Maroc</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900">
    <nav class="bg-white border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="text-2xl font-extrabold tracking-tighter text-blue-900 flex items-center">
                        <span class="text-orange-500 mr-1">Book</span>Bus
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="#" class="text-gray-500 hover:text-gray-900 font-medium text-sm">Destinations</a>
                    <a href="#" class="text-gray-500 hover:text-gray-900 font-medium text-sm">Aide</a>
                    <a href="#" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm hover:bg-blue-700 transition-all">Connexion</a>
                </div>
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer class="bg-gray-900 text-gray-400 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="col-span-1 md:col-span-2">
                    <div class="text-2xl font-extrabold text-white mb-4">
                        <span class="text-orange-500">Book</span>Bus
                    </a>
                    <p class="max-w-xs text-sm leading-relaxed">
                        Le leader du transport de voyageurs au Maroc. Réservez vos billets en ligne en toute sécurité et voyagez l'esprit tranquille.
                    </p>
                </div>
                <div>
                    <h4 class="text-white font-bold mb-4">Compagnie</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-white">À propos</a></li>
                        <li><a href="#" class="hover:text-white">Nos Gares</a></li>
                        <li><a href="#" class="hover:text-white">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-bold mb-4">Légal</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-white">CGV</a></li>
                        <li><a href="#" class="hover:text-white">Confidentialité</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-12 pt-8 text-center text-xs">
                © {{ date('Y') }} SATAS & BookBus Project. Tous droits réservés.
            </div>
        </div>
    </footer>
</body>
</html>
