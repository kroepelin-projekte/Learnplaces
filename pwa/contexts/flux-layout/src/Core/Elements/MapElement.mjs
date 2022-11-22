import * as L from './../../../libs/leaflet/dist/leaflet-src.esm.js';

export default class MapElement {

  /**
   * @var {string}
   */
  #name;
  /**
   * @var {ShadowRoot}
   */
  #shadowRoot;

  /**
   * @private
   */
  constructor() {

  }

  static async initialize() {
    const obj = new MapElement();
    await obj.#createCustomElement();
  }

  /**
   * @return {void}
   */
  async #createCustomElement() {
    const linkStyleSheet = document.getElementById('flux-layout-style');

    const styleElement = document.createElement('style');

    styleElement.innerHTML = await (await fetch(
      './contexts/flux-layout/libs/leaflet/dist/leaflet.css')).text();
    const applyShadowRootCreated = (shadowRoot) => {
      this.#shadowRoot = shadowRoot;
    }

    const tag = 'flux-map';

    customElements.define(
      tag,
      class extends HTMLElement {
        constructor() {
          super();
        }

        connectedCallback() {
          const shadowRoot = this.attachShadow({ mode: "open" });
          applyShadowRootCreated(shadowRoot);
          shadowRoot.append(styleElement);
          const mapdiv = document.createElement('div');
          mapdiv.style = "z-index: 1";
          mapdiv.id = 'mapid';
          mapdiv.style.height = "500px";
          shadowRoot.appendChild(mapdiv);

          //todo
          var map = L.map(mapdiv).setView([51.505, -0.09], 13);

          L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
          }).addTo(map);

          function onMapClick(e) {
            L.popup()
            .setLatLng(e.latlng)
            .setContent(`${e.latlng.toString()}`)
            .openOn(map);
          }
          map.on('click', onMapClick);

          this.addEventListener('slotchange', event => {
            let slots = this.querySelectorAll('slot');
            let markerGroup = L.featureGroup();
            //if(map.hasLayer(markerGroup)) {
              //todo
            markerGroup.clearLayers();
            //}
            //marker
            if (slots[0]) {
              slots[0].assignedNodes().forEach((props, key) => {
                props.childNodes.forEach((props, index) => {
                  this.setMarker(map, markerGroup, props)
                });
              })
            }

            //mapCoordinates
            if (slots[1]) {
              slots[1].assignedNodes().forEach((elementList, index) => {
                this.changeMapCoordinates(map, markerGroup, elementList)
              });
            }
          });
        }

        setMarker(map, markerGroup, coordinates, radius) {
          L.circle([
            coordinates.childNodes[0].getAttribute('content'),
            coordinates.childNodes[1].getAttribute('content')
          ], {
            color: 'violet',
            fillColor: '#f03',
            fillOpacity: 0.5,
            radius: radius
          }).addTo(markerGroup).bindPopup( coordinates.childNodes[1].getAttribute('content'));
          map.addLayer(markerGroup);
        }

        changeMapCoordinates(map, markerGroup, coordinates) {
          map.setView([
            coordinates.childNodes[0].getAttribute('content'),
            coordinates.childNodes[1].getAttribute('content')
            ],
            coordinates.childNodes[3].getAttribute('content'));
          //radius
          if(coordinates.childNodes[2].getAttribute('content') > 0) {
            this.setMarker(map, markerGroup, coordinates, coordinates.childNodes[2].getAttribute('content'))
          }
        }
      }
    );
  }

}