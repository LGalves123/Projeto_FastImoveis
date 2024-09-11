document.addEventListener('DOMContentLoaded', function() {
    let items = document.querySelectorAll('.slider .list .item');
    let next = document.getElementById('next');
    let prev = document.getElementById('prev');
    let thumbnails = document.querySelectorAll('.thumbnail .item');

    // config param
    let countItem = items.length;
    let itemActive = 0;

    function updateBackground(index = 0) {
        const sliderItems = document.querySelectorAll('.slider .list .item');
        const imageURL = sliderItems[index].querySelector('img').src;
        document.body.style.backgroundImage = `url('${imageURL}')`;
    }

    function showSlider() {
        // remove item active old
        let itemActiveOld = document.querySelector('.slider .list .item.active');
        let thumbnailActiveOld = document.querySelector('.thumbnail .item.active');
        if (itemActiveOld && thumbnailActiveOld) {
            itemActiveOld.classList.remove('active');
            thumbnailActiveOld.classList.remove('active');
        }

        // active new item
        items[itemActive].classList.add('active');
        thumbnails[itemActive].classList.add('active');

        // Update background image
        updateBackground(itemActive);

        // clear auto time run slider
        clearInterval(refreshInterval);
        refreshInterval = setInterval(() => {
            next.click();
        }, 5000);
    }

    // event next click
    next.onclick = function() {
        itemActive = itemActive + 1;
        if (itemActive >= countItem) {
            itemActive = 0;
        }
        showSlider();
    }

    // event prev click
    prev.onclick = function() {
        itemActive = itemActive - 1;
        if (itemActive < 0) {
            itemActive = countItem - 1;
        }
        showSlider();
    }

    // click thumbnail
    thumbnails.forEach((thumbnail, index) => {
        thumbnail.addEventListener('click', () => {
            itemActive = index;
            showSlider();
        });
    });

    // auto run slider
    let refreshInterval = setInterval(() => {
        next.click();
    }, 5000);

    // Initialize the background with the first image
    updateBackground();
});
