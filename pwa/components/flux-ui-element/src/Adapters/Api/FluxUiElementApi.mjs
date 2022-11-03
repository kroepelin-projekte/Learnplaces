import Service from '../../Core/Ports/Service.mjs';
import {
  HtmlLayoutRendered
} from '../../../../flux-ui-element-collection/Core/Domain/DomainEvents.mjs';

export default class FluxUiElementApi {

  /**
   * @var {Configs}
   */
  #configs;

  /**
   * @var {Service}
   */
  #service;

  /**
   * @param {FluxUiContextKeeperConfigs} configs
   */
  static create(configs) {
    return new this(configs);
  }

  /**
   * @param {FluxUiContextKeeperConfigs} configs
   * @private
   */
  constructor(configs) {
    this.#configs = configs;
    this.#service = Service.new(
      configs.domainEventPublisher
    );
  }

  //todo by channel
  render(html) {
   /* const shadowRoot = document.body.attachShadow({ mode: "open" });
    const template =  document.createElement('div');
    template.innerHTML =  html;
    shadowRoot.append(template)*/
  }

}