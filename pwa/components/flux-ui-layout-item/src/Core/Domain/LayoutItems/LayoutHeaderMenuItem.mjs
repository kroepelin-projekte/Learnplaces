const layoutHeaderMenuItem = {}

layoutHeaderMenuItem.contextId = "flux-layout-item";
layoutHeaderMenuItem.id = "flux-layout-header-menu-item";
layoutHeaderMenuItem.templateId = layoutHeaderMenuItem.id + '-template';
layoutHeaderMenuItem.tagName = "flux-layout-header-menu-item";


/**
 * @type {{templateId: string, tagName: string, template: string, id: string}}
 */
layoutHeaderMenuItem.template = new class FluxLayoutHeaderMenuItem {
  id = layoutHeaderMenuItem.id;
  tagName = layoutHeaderMenuItem.tagName;
  templateId = layoutHeaderMenuItem.templateId;
  template = '<template id="' + this.templateId + '">' +
    '    <button class="SelectMenu-item" role="menuitem" id="#">#</button> ' +
    '</template>';

  constructor() {

  }
}

export default layoutHeaderMenuItem;

