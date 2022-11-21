import * as L from './../../../node_modules/leaflet/dist/leaflet-src.esm.js';

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

  static async new() {
    const obj = new MapElement();
    obj.#createCustomElement();
    return obj;
  }

  /**
   * @return {void}
   */
  async #createCustomElement() {
    const linkStyleSheet = document.getElementById('flux-layout-style');

    const styleElement = document.createElement('style');

    styleElement.innerHTML = await (await fetch('./contexts/flux-layout/node_modules/leaflet/dist/leaflet.css')).text();
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


          this.addEventListener('slotchange', event => {

            let slots = this.querySelectorAll('slot');
            slots[0].assignedNodes().forEach(
              (element, key) => {
                element.childNodes.forEach((
                  marker, markerIndex) => {
                  //longitude
                  const longitude = marker.firstChild.getAttribute('content');
                  //latitude
                  const latitude = marker.lastChild.getAttribute('content');

                  L.marker([longitude, latitude], 13).addTo(map);

                });
              }
            )




          });

        }
      }
    );
  }

}