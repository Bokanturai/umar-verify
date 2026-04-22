/* Theme Management Script for Smart Idea */

(function () {
    const themeToggles = document.querySelectorAll('.theme-toggle-trigger, #theme-toggle');
    const darkIcons = document.querySelectorAll('.theme-toggle-dark-icon');
    const lightIcons = document.querySelectorAll('.theme-toggle-light-icon');
    const themeCheckbox = document.querySelector('input.theme-switch-input');

    // Change the icons inside the button based on previous settings
    function updateIcons(isDark) {
        darkIcons.forEach(icon => {
            if (isDark) icon.classList.add('d-none');
            else icon.classList.remove('d-none');
        });
        lightIcons.forEach(icon => {
            if (isDark) icon.classList.remove('d-none');
            else icon.classList.add('d-none');
        });
    }

    // Initialize theme
    const savedTheme = localStorage.getItem('theme');
    const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const isInitialDark = savedTheme === 'dark' || (!savedTheme && systemPrefersDark);

    if (isInitialDark) {
        document.documentElement.classList.add('dark');
        updateIcons(true);
        themeToggles.forEach(toggle => {
            if (toggle.type === 'checkbox') toggle.checked = true;
        });
    } else {
        document.documentElement.classList.remove('dark');
        updateIcons(false);
        themeToggles.forEach(toggle => {
            if (toggle.type === 'checkbox') toggle.checked = false;
        });
    }

    // Toggle theme logic
    themeToggles.forEach(toggle => {
        const toggleEvent = toggle.type === 'checkbox' ? 'change' : 'click';
        toggle.addEventListener(toggleEvent, function () {
            let isDark;
            if (toggle.type === 'checkbox') {
                isDark = toggle.checked;
            } else {
                isDark = document.documentElement.classList.toggle('dark');
            }
            
            if (isDark) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
            
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            updateIcons(isDark);
            
            // Sync other toggles
            themeToggles.forEach(other => {
                if (other !== toggle && other.type === 'checkbox') {
                    other.checked = isDark;
                }
            });
            
            // Notify other scripts/components if needed
            window.dispatchEvent(new CustomEvent('theme-changed', { detail: { theme: isDark ? 'dark' : 'light' } }));
        });
    });

    // Listen for system changes if no manual override
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
        if (!localStorage.getItem('theme')) {
            const newIsDark = e.matches;
            if (newIsDark) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
            updateIcons(newIsDark);
        }
    });

})();
