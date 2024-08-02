<script>
</script>

<div class="dummy-data">
    <div class="shimmer animation"></div>
    <div class="inner-wrapper">
        <div class="dummy-heading"></div>
        <div class="dummy-copy">
            <span class="animation"></span>
            <span class="animation"></span>
            <span class="animation"></span>
            <span class="animation new_paragraph"></span>
            <span class="animation"></span>
        </div>
    </div>
</div>

<style>
    .dummy-data {
        position: relative;
        background: #fff;
        border: none;
        /*min-height: 200px;*/
    }
    .dummy-heading {
        background: #e2e2e2;
        width: 100%;
        height: 3em;
        border-radius: 3px;
    }

    @-webkit-keyframes placeHolderShimmer {
        0% {
            left: -100%;
        }
        55% {
            left: 100%;
        }
        100% {
            left: 100%;
        }
    }

    @keyframes placeHolderShimmer {
        0% {
            left: -100%;
        }
        55% {
            left: 100%;
        }
        100% {
            left: 100%;
        }
    }

    @-webkit-keyframes placeHolderColorShift {
        0% {
            background: #dcdada;
        }
        55% {
            background: #eae8e8;
        }
        100% {
            background: #dcdada;
        }
    }

    @keyframes placeHolderColorShift {
        0% {
            background: #dcdada;
        }
        55% {
            background: #eae8e8;
        }
        100% {
            background: #dcdada;
        }
    }

    .animation {
        -webkit-animation-duration: 2.5s;
        animation-duration: 2.5s;
        -webkit-animation-fill-mode: forwards;
        animation-fill-mode: forwards;
        -webkit-animation-iteration-count: infinite;
        animation-iteration-count: infinite;
        -webkit-animation-timing-function: ease-out;
        animation-timing-function: ease-out;
    }

    .shimmer {
        background-image: linear-gradient(
            125deg,
            rgba(255, 255, 255, 0) 15%,
            rgba(255, 255, 255, 0.3) 40%,
            rgba(255, 255, 255, 0) 50%,
            rgba(255, 255, 255, 0) 100%
        );
        background-repeat: repeat-y;
        background-size: 80% 40%;
        left: -100%;
        z-index: 2;
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        -webkit-animation-name: placeHolderShimmer;
        animation-name: placeHolderShimmer;
    }

    .dummy-copy {
        background: #fff;
        height: 100px;
        width: 100%;
    }
    .dummy-copy span {
        background: #eae8e8;
        height: 0.75em;
        width: 100%;
        display: block;
        float: left;
        margin-top: 0.75em;
        -webkit-animation-name: placeHolderColorShift;
        animation-name: placeHolderColorShift;
    }
    .dummy-copy span.new_paragraph {
        margin-top: 2em;
    }
</style>
