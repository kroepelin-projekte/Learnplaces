const layoutHeader = {}

layoutHeader.contextId = "flux-layout-item";
layoutHeader.id = "flux-layout-header";
layoutHeader.templateId = layoutHeader.id + '-template';
layoutHeader.tagName = "flux-layout-header";

/**
 * @type {{templateId: string, tagName: string, template: string, id: string}}
 */
layoutHeader.template = new class FluxLayoutHeaderElement {
    id = layoutHeader.id;
    tagName = layoutHeader.tagName;
    templateId = layoutHeader.templateId;
    template = '<template id="' + this.templateId + '">' +
      '<div class="Header" style="height: 60px;">\n' +
      '<slot name="flux-layout-header-menu"></slot>' +
      '</div>\n'  +
      '</template>';

    constructor() {
    }
}

export default layoutHeader;