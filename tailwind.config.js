import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.js", // รวมไฟล์ JS ที่ใช้ Tailwind
        "./resources/css/**/*.css", // รวมไฟล์ CSS ที่ใช้ Tailwind
        "./resources/**/*.{html,php}", // ถ้าใช้ HTML, PHP ที่มีคลาส Tailwind // ตั้งค่าเส้นทางที่ Tailwind จะค้นหาคลาส
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Figtree", ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
