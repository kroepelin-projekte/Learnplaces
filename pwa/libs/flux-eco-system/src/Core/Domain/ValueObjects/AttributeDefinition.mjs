// Season enums can be grouped as static members of a class
import {ChannelDefinition} from "../Definitions/ChannelDefinition.mjs";

export class AttributeDefinition {
    /**
     * @var {string}
     */
    name
    /**
     * @var {PointerDefinition}
     */
    mapping
    /**
     * @var {Schema}
     */
    schema


    constructor(name, mapping, schema) {
        this.name = name;
        this.mapping = mapping;
        this.schema = schema;
    }


    static new(name, mapping, schema) {
        return new this(name, mapping, schema)
    }

    /**
     *
     * @param {object} json
     * @returns {Promise<AttributeDefinition>}
     */
    static async fromJson(json) {
        return new this(json.name, json.mapping, json.schema)
    }

}