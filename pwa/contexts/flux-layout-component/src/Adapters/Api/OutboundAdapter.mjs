import FluxMessageStreamApi
  from '../../../../flux-message-stream/src/Adapters/Api/FluxMessageStreamApi.mjs';
import fluxLayoutHeaderTemplate from "../../../behaviors/templates/flux-layout-header-template.mjs";

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
    return await this.#importJsonSchema(this.#behaviorsDirectoryPath + '/api/flux-layout-component.asyncapi.json');
  }

  disableLog() {name
    this.#logEnabled = false;
    this.#messageStreamApi.logEnabled = false;
  }

  onEvent(id = null) {
    let actor = "flux-layout-component";
    if(id !== null) {
      actor = actor + "/" + id;
    }
    return  this.#messageStreamApi.onEvent(actor)
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
        console.log(new DOMParser().parseFromString('fluxLayoutHeaderTemplate', 'text/html'));
        return new DOMParser().parseFromString(fluxLayoutHeaderTemplate, 'text/html').querySelector('template');
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