
import mapboxgl from 'mapbox-gl';
mapboxgl.workerClass = require('worker-loader!mapbox-gl/dist/mapbox-gl-csp-worker').default;
//mapboxgl.accessToken = 'pk.eyJ1IjoiaXRzLWFnZW5jeSIsImEiOiJjbDVnazY1bTEwMGJ5M2ZzM3ZseTg4NzV5In0.gHIvd_FQ7JitJ4VnYw0FIQ';
mapboxgl.accessToken = 'pk.eyJ1IjoibGp0ZXN0IiwiYSI6ImNsNmRpaGxuYTBla3Qzam4yemlqNG1nMTYifQ._Virq8ORb4S2uSqXIsOixQ';
import { breakpoints }  from '~/js/helpers/breakpoints'
import { open } from '~/blocks/modal/modal';

const mapInit = (container, single) => {
  let currentRegion;
  
  const mapMarkers = [];
  
  regionsMapData.forEach((region) => {
    if(region.active)
      currentRegion = region.features[0].geometry.coordinates;
  })

  const map = new mapboxgl.Map({
    container: container,
    //style: 'mapbox://styles/its-agency/cl5kxb3id009g14nvirtf5h9e',
    style: 'mapbox://styles/ljtest/cl6dilx3i000p15pmq681gfzx',
    center: currentRegion,
    zoom: 14
  });
  map.addControl(new mapboxgl.NavigationControl());

  const setActivePoint = (id) => {
    $.each('[data-map-point]', point => {
      if(point.closest(".sale-points__item"))
        point.closest(".sale-points__item").classList.remove("is-active");
    })
    
    $.qs(`[data-map-point="${id}"]`).closest(".sale-points__item").classList.add("is-active");
    $.qs(`[data-map-point="${id}"]`).closest(".sale-points__item").scrollIntoView({block: "center", behavior: "smooth"});
  }

  const setMapMarkers = (data) => {
    let regionMarkers = [];
    if(mapMarkers.length > 0){
      mapMarkers.forEach((el) => {
        el.remove();
      })
    }

    for (const { geometry, id } of data.features) {
      const el = document.createElement('div');
      el.innerText = "LJi";
      el.className = 'map__marker';
      const marker = new mapboxgl.Marker(el).setLngLat(geometry.coordinates).addTo(map);
      mapMarkers.push(marker)
      marker._element.addEventListener("click", () => {
        setActivePoint(id);
      })
      regionMarkers.push(geometry.coordinates)
    }

    if(regionMarkers.length > 1){
      map.fitBounds(regionMarkers, {padding: 300});
      data.features.forEach((el) => {
         if(el.centered){
           flyTo(el.geometry.coordinates)
         }
      })
    }
  }

  const flyTo = (center) => {
    map.flyTo({
        center: center,
        zoom: 15,
        bearing: 0,
        speed: 2,
        curve: 1,
        essential: true
    });
  }

  const setPointListeners = () => {
    $.each('[data-map-point]', point => {
      point.addEventListener("click", (e) => {
        currentRegion.features.forEach((feature) => {
          if(point.dataset.mapPoint == feature.id){
            flyTo(feature.geometry.coordinates)
            openModal(e);
          }
        });

        $.qsa(".sale-points__item").forEach((el) => {
          el.classList.remove("is-active");
        });

        point.closest(".sale-points__item").classList.add("is-active");
      })
    })
  }

  const openModal = (e) => {
    const info = e.target.closest(".point-info").cloneNode(true);
    let mapModal;
    $.each('[data-modal]', (modal) => {
      if(modal.dataset.modal == "map-modal")
        mapModal = modal;
    })
    $.qs(".js-contacts-modal-content", mapModal).innerHTML = "";
    $.qs(".js-contacts-modal-content", mapModal).appendChild(info);
    open(mapModal);
  }

  map.on('style.load', () => {
      const waiting = () => {
        if (!map.isStyleLoaded()) {
          setTimeout(waiting, 200);
          $.qs(".js-contacts").classList.add("is-loaded");
        }
        else{
          if(single){
            flyTo(currentRegion);
            $.qsa(".map").forEach((el) => el.classList.add("is-loaded"))
          }  
        }
      };
      waiting();
  });

  document.addEventListener("regionChange", (e) => {
    regionsMapData.forEach((region) => {
      if(region.id == e.detail.id){
        currentRegion = region;
      }
    })
    setMapMarkers(currentRegion);
    setTimeout(() => {
      $.qsa(".map").forEach((el) => el.classList.add("is-loaded"))
    }, 1200)
  })

  if(single){
    regionsMapData.forEach((region) => {
      if(region.active){
        setMapMarkers(region);
      }
    })
  }

  setPointListeners();
}

$.each('[data-map=single]', (map) => {
  mapInit(map, true);
})

$.each('[data-map-platform=desktop]', (map) => {
  if(window.innerWidth > breakpoints.desktop)
    mapInit(map, false);
})

$.each('[data-map-platform=mobile]', (map) => {
  if(window.innerWidth <= breakpoints.desktop)
    mapInit(map, false);
})
