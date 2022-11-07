const appElementArtefact = {}

appElementArtefact.contextId = "flux-app";
appElementArtefact.id = "flux-app";
appElementArtefact.tagName = "flux-app";
appElementArtefact.templateId = appElementArtefact.id + '-template';
appElementArtefact.slotNameComponentApi = "flux-component-api";

appElementArtefact.appElement = new class FluxAppElement {
    id = appElementArtefact.id;
    tagName = appElementArtefact.tagName;
    templateId =  appElementArtefact.templateId;
    template = '<template id="' + appElementArtefact.templateId + '">' +
      '<slot name="'+ appElementArtefact.slotNameComponentApi +'">' +
      '<data slot="'+ appElementArtefact.slotNameComponentApi +'" value="' + {"tag": "example-tag", "src":"./components/example-component/Adapters/Api/ExampleApi.mjs"} +'"></data>' +
      '</slot>\n' +
      '</template>';

    /**
     * @return {{id: string, tagName: string, templateId: string, template: string}}
     */
    constructor() {

    }
}

export default appElementArtefact;