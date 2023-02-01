import {ReceiveDefinition} from "./ReceiveDefinition.mjs";
import {ChannelDefinition} from "./ChannelDefinition.mjs";
import {ActionType} from "./ActionType.mjs";
import {Schema as Inputs} from "../ValueObjects/Schema.mjs";
import {Keyword} from "./Keyword.mjs";

export class ApplicationDefinition {
    /**
     * @var {string}
     */
    id
    /**
     * @var {string}
     */
    apiFilePath
    /**
     * @var {Map.<string,{ReceiveDefinition}>}
     */
    receives;
    /**
     * @var {Map.<string,{ChannelDefinition}>}
     */
    sends


    constructor(id, apiFilePath, receives, sends) {
        this.id = id;
        this.apiFilePath = apiFilePath;
        this.receives = receives;
        this.sends = sends;
    }


    static new(name, apiFilePath, receives, sends) {
        return new this(name, apiFilePath, receives, sends)
    }

    /**
     *
     * @param {object} appDefinitionJson
     * @param {object} appConfigJson
     * @returns {Promise<ApplicationDefinition>}
     */
    static async fromJson(appDefinitionJson, appConfigJson) {
        return new this(appDefinitionJson[Keyword.ID.value], appConfigJson[Keyword.API_FILE_PATH.value], appConfigJson[Keyword.RECEIVES.value], appDefinitionJson[Keyword.SENDS.value])
    }

    /**
     * @param {ActionType} channelType
     * @param {string} messageName
     * @returns {boolean}
     */
    exists(channelType, messageName) {
        if (channelType.match(ActionType.RECEIVES) === true) {
            return (this.sends.hasOwnProperty(messageName));
        }

        if (channelType.match(ActionType.SENDS) === true) {
            return (this.receives.hasOwnProperty(messageName));
        }
    }

}