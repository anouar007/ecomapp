const driver = window.driver.js.driver;

const driverObj = driver({
    showProgress: true,
    animate: true,
    allowClose: true,
    doneBtnText: 'Finish',
    nextBtnText: 'Next',
    prevBtnText: 'Previous',
    steps: [
        {
            element: '.navbar-brand',
            popover: {
                title: 'Welcome to Speed E-commerce!',
                description: 'This is your new central hub for managing your entire business. Let\'s take a quick tour to get you started.',
                side: 'bottom',
                align: 'start'
            }
        },
        {
            element: '.sidebar',
            popover: {
                title: 'Main Navigation',
                description: 'This sidebar is your primary way to move around. Access Products, Orders, Customers, Inventory, and more from here.',
                side: 'right',
                align: 'start'
            }
        },
        {
            element: '.dashboard-stats',
            popover: {
                title: 'At a Glance',
                description: 'See your key business metrics instantly. Track Users, Orders, Revenue, and Products in real-time.',
                side: 'bottom',
                align: 'center'
            }
        },
        {
            element: '.navbar-user',
            popover: {
                title: 'Your Profile',
                description: 'Manage your personal account settings, profile information, and log out from here.',
                side: 'left',
                align: 'start'
            }
        },
        {
            element: 'button[onclick="startTour()"]',
            popover: {
                title: 'Need Help?',
                description: 'Click this button anytime you want to replay this guide or access help resources.',
                side: 'bottom',
                align: 'end'
            }
        }
    ]
});

function startTour() {
    driverObj.drive();
}

// Auto-start tour if specific query param is present or valid for first-time users (using localStorage)
document.addEventListener('DOMContentLoaded', () => {
    if (!localStorage.getItem('tour_seen')) {
        // Optional: Add a small delay to ensure page rendering
        setTimeout(() => {
            driverObj.drive();
            localStorage.setItem('tour_seen', 'true');
        }, 1000);
    }
});
