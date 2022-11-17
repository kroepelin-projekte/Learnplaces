import FluxMessageStreamApi
  from '../../../../flux-message-stream/src/Adapters/Api/FluxMessageStreamApi.mjs';
import fluxLayoutHeaderTemplate from "../../../behaviors/templates/flux-layout-header-template.mjs";
import fluxLayoutMenuTemplate from "../../../behaviors/templates/flux-layout-menu-template.mjs";
import fluxLayoutMenuItemTemplate from "../../../behaviors/templates/flux-layout-menu-item-template.mjs";
import fluxLayoutMenuTitleTemplate from "../../../behaviors/templates/flux-layout-menu-title-template.mjs";
import fluxLayoutContentContainerTemplate from "../../../behaviors/templates/flux-layout-content-container-template.mjs";
import fluxLayoutMapTemplate from "../../../behaviors/templates/flux-layout-map-template.mjs";

export default class OutboundAdapter {

  #logEnabled = true;
  #messageStreamApi;
  /** @var string */
  #behaviorsDirectoryPath;


  /**
   * @param {{behaviorsDirectoryPath: string}} payload
   */
  static initialize(payload) {
    return new this(payload)
  }

  /**
   * @private
   */
  constructor(payload) {
    this.#behaviorsDirectoryPath = payload.behaviorsDirectoryPath;
    this.#messageStreamApi =  FluxMessageStreamApi.initialize(this.#logEnabled)
  }

  async getApiBehaviors() {
    return await this.#importJsonSchema(this.#behaviorsDirectoryPath + '/api/flux-layout-component.api.json');
  }

  disableLog() {name
    this.#logEnabled = false;
    this.#messageStreamApi.logEnabled = false;
  }



  eventStream(actorAddress) {
    return  this.#messageStreamApi.onEvent(actorAddress)
  }

  onRegister(name) {
    return this.#messageStreamApi.onRegister(name)
  }



  async importCss() {
    const path = this.#appendBaserUrl(this.#behaviorsDirectoryPath + "/css/stylesheet.css");
    const response = await (await fetch(path));
    return  await response.text();
  }


  /**
   * @param {string} templateId
   * @return {Promise<any>}
   */
  async loadTemplate(templateId) {

    //TODO
    switch(templateId) {
      case 'flux-layout-header-template':
        return new DOMParser().parseFromString(fluxLayoutHeaderTemplate, 'text/html').querySelector('template');
      case 'flux-layout-menu-template':
        return new DOMParser().parseFromString(fluxLayoutMenuTemplate, 'text/html').querySelector('template');
      case 'flux-layout-menu-title-template':
        return new DOMParser().parseFromString(fluxLayoutMenuTitleTemplate, 'text/html').querySelector('template');
      case 'flux-layout-menu-item-template':
        return new DOMParser().parseFromString(fluxLayoutMenuItemTemplate, 'text/html').querySelector('template');
      case 'flux-layout-content-container-template':
        return new DOMParser().parseFromString(fluxLayoutContentContainerTemplate, 'text/html').querySelector('template');
      case 'flux-layout-map-template':
        return new DOMParser().parseFromString(fluxLayoutMapTemplate, 'text/html').querySelector('template');
      }
  }

  async #importApiClass(src) {
    const ApiClass = await (await (import(this.#appendBaserUrl(src))));
    return ApiClass.default;
  }

  async #importJsonSchema(src) {
    const templateFilePath = this.#appendBaserUrl(src)
    const response =  await (await fetch(templateFilePath, { assert: { type: 'json' } }));
    return await response.json();
  }

  /**
   * @param {string} src
   * @return {string}
   */
  #appendBaserUrl(src) {
    const baseUrl = document.getElementById("flux-pwa-base");
    return baseUrl.href + "/" + src;
  }
}