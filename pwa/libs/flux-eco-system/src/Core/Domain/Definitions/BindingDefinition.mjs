export class BindingDefinition {

    /**
     * @var {DefinitionKeyword}
     */
    bindingType
    /**
     * @var {object}
     */
    parameters

    /**
     * @param {BindingType} bindingType
     * @param {object} parameters
     */
    constructor(bindingType, parameters) {
        this.bindingType = bindingType;
        this.parameters = parameters;
    }

    /**
     * @param {BindingType} bindingType
     * @param {object} parameters
     */
    static new(bindingType, parameters) {
        return new self(bindingType, parameters);
    }
}