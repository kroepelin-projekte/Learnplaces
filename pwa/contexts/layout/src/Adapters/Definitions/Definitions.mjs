import Header from "../../../definitions/templates/header.mjs";
import Menu from "../../../definitions/templates/menu.mjs";
import MenuItem from "../../../definitions/templates/menu-item.mjs";
import String from "../../../definitions/templates/string.mjs";
import ContentContainer from "../../../definitions/templates/content-container.mjs";
import Map from "../../../definitions/templates/map.mjs";
import MapMarker from "../../../definitions/templates/map-marker.mjs";
import MapLocation from "../../../definitions/templates/map-location.mjs";


export default class Definitions {

  #definitionsPath;

  /**
   * @private
   */
  constructor(baseUrl) {
    this.#definitionsPath = baseUrl;
  }

  /**
   * @param baseUrl
   * @return {Promise<Definitions>}
   */
  static async new(baseUrl) {
    return new Definitions(baseUrl);
  }

  async apiDefinition() {
    return await this.#importJsonSchema(this.#definitionsPath + '/api/api.json');
  }

  /**
   * @param {string} templateId
   * @return {Promise<any>}
   */
  async template(templateId) {

    //TODO
    switch(templateId) {
      case 'header-template':
        return new DOMParser().parseFromString(Header, 'text/html').querySelector('template');
      case 'menu-template':
        return new DOMParser().parseFromString(Menu, 'text/html').querySelector('template');
      case 'string-template':
        return new DOMParser().parseFromString(String, 'text/html').querySelector('template');
      case 'menu-item-template':
        return new DOMParser().parseFromString(MenuItem, 'text/html').querySelector('template');
      case 'content-container-template':
        return new DOMParser().parseFromString(ContentContainer, 'text/html').querySelector('template');
      case 'map-template':
        return new DOMParser().parseFromString(Map, 'text/html').querySelector('template');
      case 'map-marker-template':
        return new DOMParser().parseFromString(MapMarker, 'text/html').querySelector('template');
      case 'map-location-template':
        return new DOMParser().parseFromString(MapLocation, 'text/html').querySelector('template');
    }
  }


  async css() {
    const path = this.#definitionsPath + "/css/stylesheet.css";
    const response = await (await fetch(path));
    return  await response.text();
  }

  async #importJsonSchema(src) {
    const response = await (await fetch(src, { assert: { type: 'json' } }));
    return await response.json();
  }

}