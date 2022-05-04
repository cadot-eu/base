import Splide from "@splidejs/splide";
//import Video from "@splidejs/splide-extension-video";
import { AutoScroll } from '@splidejs/splide-extension-auto-scroll';
import "@splidejs/splide/dist/css/splide.min.css";
document.addEventListener('DOMContentLoaded', function () {
  if ($('#image-slider').length)
    var splide = new Splide('#image-slider', {
      height: '330px',
      pauseOnHover: true,
      interval: 4000,
      autoplay: true,
      type: 'loop',
      speed: '5000',
      perPage: 2,
      breakpoints: {
        750: {
          perPage: 1,
        },
      },
    }).mount();

  if ($('.splide-avis').length)
    var splideavis = new Splide('.splide-avis', {
      type: 'loop',
      direction: 'ttb',
      height: '13rem',
      focus: 'center',
      autoHeight: true,
      pauseOnHover: true,
      type: 'loop',
    }).mount({ AutoScroll });

  if ($('#pro-slider').length)
    var splidepro = new Splide('#pro-slider', {
      height: '100px',
      pauseOnHover: true,
      type: 'loop',
      //speed: '2000',
      perPage: 3
    }).mount({ AutoScroll });
});

