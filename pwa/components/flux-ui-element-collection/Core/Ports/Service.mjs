import Aggregate from "../Domain/Aggregate.mjs";

export default class Service {
    /**
     * @return Service
     */
    static new() {
        return new this();
    }

    constructor() {

    }

    create(id) {
        const aggregate = Aggregate.create(id);
        aggregate.create()
    }

    renderHtmlLayout(id, htmlLayout) {
        const aggregate = Aggregate.create(id);
        aggregate.renderHtmlLayout(htmlLayout);
    }

    async onShadowRootCreated(id) {
        const aggregate = Aggregate.create(id);

        const links = await (await fetch('/api/flux-menu/edit-students.json')).json();
        aggregate.setLinks(links);
    }
}