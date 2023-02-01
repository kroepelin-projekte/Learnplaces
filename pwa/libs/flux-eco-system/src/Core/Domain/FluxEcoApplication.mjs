export class FluxEcoApplication {
    /**
     * @var {object}
     */
    applicationApi
    /**
     * @var {ApplicationDefinition}
     */
    applicationDefinition


    constructor(applicationApi, applicationDefinition) {
        this.applicationApi = applicationApi;
        this.applicationDefinition = applicationDefinition;
    }


    static new(applicationApi, applicationDefinition) {
        return new this(applicationApi, applicationDefinition)
    }
}