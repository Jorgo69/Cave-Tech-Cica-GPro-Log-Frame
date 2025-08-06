import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms'; // Assurez-vous d'avoir installé @tailwindcss/forms si vous l'utilisez

/** @type {import('tailwindcss').Config} */
export default {
  // Active le mode sombre basé sur la classe 'dark' sur l'élément HTML
  darkMode: 'class',

  content: [
    // Chemins par défaut pour les vues Blade de Laravel et les fichiers de pagination
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php', // Ceci est essentiel pour vos propres vues Blade

    // Chemins spécifiques à Livewire pour que Tailwind scanne les classes utilisées dans vos composants
    './vendor/livewire/livewire/src/Features/SupportFileUploads/FileUploadsServiceProvider.php',
    './vendor/livewire/livewire/src/LivewireServiceProvider.php',
    // Si vous créez des classes Livewire, assurez-vous que leurs vues associées sont scannées.
    // Par exemple, si vous avez des vues dans resources/views/livewire/..., le chemin ci-dessus les couvrira.
  ],

  theme: {
    extend: {
      // Définition des polices
      fontFamily: {
        // 'sans' utilisera Inter, Poppins, Roboto ou la police par défaut du système
        sans: ['Inter', 'Poppins', 'Roboto', ...defaultTheme.fontFamily.sans], // J'ai ajouté defaultTheme.fontFamily.sans comme dernière option
      },
      // Palette de couleurs personnalisée pour une interface moderne
      colors: {
        primary: {
          light: '#6366F1', // Indigo 500
          DEFAULT: '#4F46E5', // Indigo 600
          dark: '#4338CA', // Indigo 700
        },
        secondary: {
          light: '#A78BFA', // Violet 400
          DEFAULT: '#8B5CF6', // Violet 500
          dark: '#7C3AED', // Violet 600
        },
        // Couleurs de fond pour les modes clair et sombre
        background: {
          light: '#F9FAFB', // Gray 50
          dark: '#1F2937',  // Gray 800 (Note: assurez-vous que cette couleur contraste bien avec le texte dark)
        },
        // Couleurs de texte pour les modes clair et sombre
        text: {
          light: '#111827', // Gray 900
          dark: '#F9FAFB',  // Gray 50
        },
        // Couleurs pour les cartes et conteneurs
        card: {
          light: '#FFFFFF', // White
          dark: '#374151',  // Gray 700
        },
        // Couleurs des bordures
        border: {
          light: '#E5E7EB', // Gray 200
          dark: '#4B5563',  // Gray 600
        },
        // Couleurs de succès, erreur, avertissement, info
        success: {
          DEFAULT: '#10B981', // Emerald 500
          dark: '#059669', // Emerald 600
        },
        error: {
          DEFAULT: '#EF4444', // Red 500
          dark: '#DC2626', // Red 600
        },
        warning: {
          DEFAULT: '#F59E0B', // Amber 500
          dark: '#D97706', // Amber 600
        },
        info: {
          DEFAULT: '#3B82F6', // Blue 500
          dark: '#2563EB', // Blue 600
        }
      },
    },
  },
  plugins: [forms], // J'ai remis 'forms' ici car il est souvent utile avec Breeze
}