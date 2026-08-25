<style>
    /* Vitória Hospitalar Custom Red & White/Dark Branding Styles */
    :root {
        --vh-primary: #dc2626;
        --vh-primary-dark: #b91c1c;
        --vh-primary-light: #fef2f2;
    }

    /* Light mode specific */
    html:not(.dark) .fi-topbar {
        background-color: #ffffff !important;
        border-bottom: 1px solid #e2e8f0 !important;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);
    }

    html:not(.dark) .fi-sidebar-item-active > a,
    html:not(.dark) .fi-sidebar-item-active > button {
        background-color: #fef2f2 !important;
        color: #dc2626 !important;
        border-left: 3px solid #dc2626;
        font-weight: 600 !important;
    }

    html:not(.dark) .fi-sidebar-item-active svg {
        color: #dc2626 !important;
    }

    html:not(.dark) .fi-section, 
    html:not(.dark) .fi-ta-ctn {
        border-radius: 1rem !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.04), 0 2px 4px -1px rgba(0, 0, 0, 0.02) !important;
        border: 1px solid #f1f5f9 !important;
    }

    /* Dark mode specific */
    .dark .fi-topbar {
        background-color: #0f172a !important;
        border-bottom: 1px solid #1e293b !important;
    }

    .dark .fi-sidebar-item-active > a,
    .dark .fi-sidebar-item-active > button {
        background-color: rgba(220, 38, 38, 0.15) !important;
        color: #f87171 !important;
        border-left: 3px solid #ef4444;
        font-weight: 600 !important;
    }

    .dark .fi-sidebar-item-active svg {
        color: #f87171 !important;
    }

    .dark .fi-section, 
    .dark .fi-ta-ctn {
        border-radius: 1rem !important;
        border: 1px solid #1e293b !important;
    }

    /* Brand logo container */
    .fi-logo {
        height: 2.75rem !important;
        display: flex;
        align-items: center;
    }

    .fi-logo img {
        height: 2.75rem !important;
        width: auto !important;
        object-fit: contain;
    }

    /* Primary buttons */
    .fi-btn-primary {
        background-color: #dc2626 !important;
        transition: all 0.2s ease-in-out;
    }
    .fi-btn-primary:hover {
        background-color: #b91c1c !important;
        transform: translateY(-1px);
    }
</style>
