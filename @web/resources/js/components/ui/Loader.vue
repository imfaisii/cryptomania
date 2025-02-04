<template>
    <div class="loaderContainer">
        <div class='loader'><div><div><div><div><div><div></div></div></div></div></div></div></div>
    </div>
</template>

<style lang="scss">
    @import "resources/sass/variables";

    .loader {
        position: relative;
        width: 150px;
        height: 150px;
        display: block;
        overflow: hidden;

        div {
            height: 100%;
        }
    }

    .loader, .loader div {
        border-radius: 50%;
        padding: 8px;
        border: 2px solid transparent;
        animation: rotateScale 3.5s linear infinite;
        border-top-color: #3abdfe;  // Static purple color
        border-bottom-color: #9d6bfe; // Static white color
    }

    @keyframes rotateScale {
        0% {
            transform: rotate(0deg) scale(1);
        }
        25% {
            transform: rotate(90deg) scale(1.1);
        }
        50% {
            transform: rotate(180deg) scale(1.2);
        }
        75% {
            transform: rotate(270deg) scale(1.1);
        }
        100% {
            transform: rotate(360deg) scale(1);
        }
    }
</style>