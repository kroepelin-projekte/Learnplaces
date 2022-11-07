const layoutHeaderMenu = {}

layoutHeaderMenu.contextId = "flux-layout-item";
layoutHeaderMenu.id = "flux-layout-header-menu";
layoutHeaderMenu.templateId = layoutHeaderMenu.id + '-template';
layoutHeaderMenu.tagName = "flux-layout-header-menu";


/**
 * @type {{templateId: string, tagName: string, template: string, id: string}}
 */
layoutHeaderMenu.template = new class FluxLayoutHeaderMenu {
    id = layoutHeaderMenu.id;
    tagName = layoutHeaderMenu.tagName;
    templateId = layoutHeaderMenu.templateId;
    template = '<template id="' + this.templateId + '">' +
      '    <div class="Header-item position-absolute right-0" slot="flux-layout-header-menu">\n' +
      '        <details class="details-reset details-overlay" open>\n' +
      '            <summary class="btn" aria-haspopup="true">\n' +
      '                <slot name="flux-layout-header-menu-title">Example</slot>' +
      '            </summary>\n' +
      '            <div class="SelectMenu right-0">\n' +
      '                <div class="SelectMenu-modal">\n' +
      '                    <div class="SelectMenu-list">\n' +
      '                        <slot name="flux-layout-header-menu-item"><button class="SelectMenu-item" role="menuitem">Example</button></slot>\n' +
      '                    </div>\n' +
      '                </div>\n' +
      '            </div>\n' +
      '        </details>\n' +
      '    </div>\n' +
      '</template>';

    constructor() {

    }
}

export default layoutHeaderMenu;