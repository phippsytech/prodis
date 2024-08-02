<script>
  
    export let rating=0;
    export let colour="rgb(234 179 8)";

    let total_stars = 5;
    let half_star = false;
    let decimal = roundToHalf(parseFloat(rating));
    let remainder = decimal%1
    let full_stars = parseInt(decimal); // this dumps the remainder if it is there
    let empty_stars = total_stars - full_stars;
    if(remainder > 0){
        half_star = true;
        empty_stars = empty_stars -1;
    }

    //src: https://www.breakingpar.com/bkp/home.nsf/0/87256b280015193f87257124007e0011
    function roundToHalf(value) {
        let converted = parseFloat(value); // Make sure we have a number
        let decimal = (converted - parseInt(converted, 10));
        decimal = Math.round(decimal * 10);
        if (decimal == 5) { return (parseInt(converted, 10)+0.5); }
        if ( (decimal < 3) || (decimal > 7) ) {
            return Math.round(converted);
        } else {
            return (parseInt(converted, 10)+0.5);
        }
    }
</script>

<ul class="flex justify-center">
  {#each {length: full_stars} as _, i}
  <li>
    <svg aria-hidden="true" focusable="false"  class="w-4 mr-1" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
      <path fill="{colour}" d="M259.3 17.8L194 150.2 47.9 171.5c-26.2 3.8-36.7 36.1-17.7 54.6l105.7 103-25 145.5c-4.5 26.3 23.2 46 46.4 33.7L288 439.6l130.7 68.7c23.2 12.2 50.9-7.4 46.4-33.7l-25-145.5 105.7-103c19-18.5 8.5-50.8-17.7-54.6L382 150.2 316.7 17.8c-11.7-23.6-45.6-23.9-57.4 0z"></path>
    </svg>
  </li>
  {/each}

{#if half_star}
<li>
  <svg aria-hidden="true" focusable="false"  class="w-4 mr-1" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
    <path fill="{colour}" d="M 288,-0 C 263.2,-0.8 256.2,25.3 246.8,43 229.2,78.7 211.6,114.4 194,150.1 c -50.2,7.5 -100.6,14.2 -150.8,22.3 -25.5,6.5 -30.1,43.4 -8.7,57.6 33.8,32.9 67.6,65.9 101.4,98.8 -8.3,50.1 -17.6,100.1 -25.4,150.3 -1.6,26.3 31.9,42 52.1,26 C 204.3,483.5 246.1,461.5 288,439.5 c 45,23.4 89.7,47.6 135,70.6 24.4,9.6 50,-17.3 41,-41.4 -7.9,-46.5 -15.9,-93.1 -23.9,-139.6 36.2,-35.6 73.1,-70.6 108.9,-106.6 16.8,-20.3 -1,-52.9 -26.7,-51.8 C 475.4,163.8 428.7,157 382,150.1 359.3,104.7 337.4,58.8 314.2,13.6 308.4,5.1 298.3,-0 288,-0 Z M 288,68 c 20.7,42 41.4,84 62.1,126 46,6.7 92.6,13.4 139,20.2 -33.5,32.6 -67,65.3 -100.5,98 7.9,46.1 15.8,92.2 23.7,138.3 C 370.8,428.9 329.4,407.1 288,385.4 c 0,-105.7 0,-211.5 0,-317.3 z"></path>
  </svg>
  </li>
{/if}

  {#each {length: empty_stars} as _, i}
  <li>
    <svg aria-hidden="true" focusable="false"  class="w-4 mr-1" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
      <path fill="{colour}" d="M528.1 171.5L382 150.2 316.7 17.8c-11.7-23.6-45.6-23.9-57.4 0L194 150.2 47.9 171.5c-26.2 3.8-36.7 36.1-17.7 54.6l105.7 103-25 145.5c-4.5 26.3 23.2 46 46.4 33.7L288 439.6l130.7 68.7c23.2 12.2 50.9-7.4 46.4-33.7l-25-145.5 105.7-103c19-18.5 8.5-50.8-17.7-54.6zM388.6 312.3l23.7 138.4L288 385.4l-124.3 65.3 23.7-138.4-100.6-98 139-20.2 62.2-126 62.2 126 139 20.2-100.6 98z"></path>
    </svg>
  </li>
  {/each}
  
</ul>