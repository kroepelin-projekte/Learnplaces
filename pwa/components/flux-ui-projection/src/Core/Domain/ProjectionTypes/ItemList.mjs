const itemList = {}

itemList.contextId = "flux-projection";
itemList.id = "flux-projection-item-list"; //todo
itemList.templateId = itemList.id + '-template';
itemList.tagName = "flux-projection-item-list";

/**
 * @type {FluxProjectionType}
 */
itemList.template = new class FluxProjectionItemList {
  id = itemList.id;
  tagName = itemList.tagName;
  templateId = itemList.templateId;
  template = '<template id="' + this.templateId + '">' +
    '<div>\n' +
    '<slot name="flux-layout-header-menu"></slot>' +
    '</div>\n'  +
    '</template>';

  constructor() {
  }
}

export default itemList;