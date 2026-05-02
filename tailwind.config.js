import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";
import typography from "@tailwindcss/typography";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./vendor/laravel/jetstream/**/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.vue",
        "./resources/js/**/*.js",
    ],

    // Safelist untuk class yang di-generate dinamis (jika ada)
    safelist: [
        // Dashboard module colors - Background gradients
        'from-blue-500', 'to-blue-600', 'from-blue-600', 'to-blue-700',
        'from-purple-500', 'to-purple-600', 'from-purple-600', 'to-purple-700',
        'from-emerald-500', 'to-emerald-600', 'from-emerald-600', 'to-emerald-700',
        'from-orange-500', 'to-orange-600', 'from-orange-600', 'to-orange-700',
        'from-indigo-500', 'to-indigo-600', 'from-indigo-600', 'to-indigo-700',
        'from-red-500', 'to-red-600', 'from-red-600', 'to-red-700',
        'from-teal-500', 'to-teal-600', 'from-teal-600', 'to-teal-700',
        'from-pink-500', 'to-pink-600', 'from-pink-600', 'to-pink-700',
        'from-cyan-500', 'to-cyan-600', 'from-cyan-600', 'to-cyan-700',
        'from-amber-500', 'to-amber-600', 'from-amber-600', 'to-amber-700',
        'from-slate-500', 'to-slate-600', 'from-slate-600', 'to-slate-700',
        
        // Icon backgrounds and borders
        'bg-blue-100', 'border-blue-200', 'text-blue-600',
        'bg-purple-100', 'border-purple-200', 'text-purple-600',
        'bg-emerald-100', 'border-emerald-200', 'text-emerald-600',
        'bg-orange-100', 'border-orange-200', 'text-orange-600',
        'bg-indigo-100', 'border-indigo-200', 'text-indigo-600',
        'bg-red-100', 'border-red-200', 'text-red-600',
        'bg-teal-100', 'border-teal-200', 'text-teal-600',
        'bg-pink-100', 'border-pink-200', 'text-pink-600',
        'bg-cyan-100', 'border-cyan-200', 'text-cyan-600',
        'bg-amber-100', 'border-amber-200', 'text-amber-600',
        'bg-slate-100', 'border-slate-200', 'text-slate-600',
        
        // Gradient backgrounds
        'bg-gradient-to-br',
        
        // Common utility classes
        'hover:scale-110', 'hover:scale-[1.05]', 'group-hover:scale-110',
        'transition-all', 'duration-300', 'duration-500', 'duration-1000',
        'shadow-lg', 'hover:shadow-xl', 'hover:shadow-2xl',
        'text-4xl', 'text-2xl', 'text-xl',
        'rounded-2xl', 'border-4',
        'opacity-0', 'group-hover:opacity-100', 'group-hover:opacity-30',
        'blur-xl', 'transition-opacity',
        'animate-pulse',
        'bg-green-400', 'w-3', 'h-3', 'rounded-full',
        '-translate-x-full', 'group-hover:translate-x-full', 'transition-transform'
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Figtree", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    50: "#eff6ff",
                    100: "#dbeafe",
                    200: "#bfdbfe",
                    300: "#93c5fd",
                    400: "#60a5fa",
                    500: "#3b82f6",
                    600: "#2563eb",
                    700: "#1d4ed8",
                    800: "#1e40af",
                    900: "#1e3a8a",
                    950: "#172554",
                },
            },
        },
    },

    plugins: [forms, typography],

    // Optimasi untuk production build
    future: {
        hoverOnlyWhenSupported: true, // Hover hanya di device yang support
    },
};
