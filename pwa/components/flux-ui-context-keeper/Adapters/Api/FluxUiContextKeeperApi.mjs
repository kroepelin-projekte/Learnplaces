import FluxUiElementConfigs
  from '../../../flux-ui-element/src/Adapters/Api/FluxUiElementConfigs.mjs';

export default class FluxUiContextKeeperApi {


  /**
   * @var {FluxUiContextKeeperConfigs}
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
   * @private
   * @param {FluxUiContextKeeperConfigs} configs
   */
  constructor( configs ) {
    this.#configs = configs
    this.addHeader();
  }

  //todo
  async addHeader() {
    const ApiClass = await (await import(this.#configs.uiElementApiSrc)).default;
    const api = ApiClass.create(
      FluxUiElementConfigs.create('header')
    )
   /* api.render(
      '<div class="Header">\n' +
      '    <div class="Header-item position-absolute right-0">\n' +
      '\n' +
      '        <details class="details-reset details-overlay" open>\n' +
      '            <summary class="btn" aria-haspopup="true">\n' +
      '           Courses\n' +
      '            </summary>\n' +
      '            <div class="SelectMenu right-0">\n' +
      '                <div class="SelectMenu-modal">\n' +
      '                    <div class="SelectMenu-list">\n' +
      '                        <button class="SelectMenu-item" role="menuitem">Course 1</button>\n' +
      '                        <button class="SelectMenu-item" role="menuitem">Course 2</button>\n' +
      '                        <button class="SelectMenu-item" role="menuitem">Course 3</button>\n' +
      '                    </div>\n' +
      '                </div>\n' +
      '            </div>\n' +
      '        </details>\n' +
      '    </div>\n' +
      ' </div>'
    )*/
  }


}