import {Config} from "../Config/Config.mjs";
import {Service} from "../../Core/Ports/Service.mjs";
import {FluxEcoBroadCastChannel} from "../BroadcastChannel/FluxEcoBroadCastChannel.mjs";
import {JsonFile} from "../../Core/Domain/ValueObjects/JsonFile.mjs";
import {DomainMessageDispatcher} from "../../Core/Ports/DomainMessageDispatcher.mjs";

export class FluxEcoSystemApi {
    /**
     * @var {Config}
     */
    #config;
    /**
     * @var {Service}
     */
    #service;

    /**
     * @private
     */
    constructor(
        config,
        service
    ) {
        this.#config = config;
        this.#service = service
    }

    /**
     * @returns {FluxEcoSystemApi}
     */
    static new() {
        const config = Config.new();
        const fluxEcoBroadCastChannel = FluxEcoBroadCastChannel.new();
        return new this(
            config,
            Service.new(
                DomainMessageDispatcher.new((domainMessage) => fluxEcoBroadCastChannel.subscribe(domainMessage))
            )
        );
    }

    /**
     * @param {string} applicationDefinitionFilePath
     * @param {string} applicationConfigFilePath
     * @returns {Promise<void>}
     */
    async connectApplication(applicationDefinitionFilePath, applicationConfigFilePath) {
        await this.#service.connectApplication(JsonFile.new(applicationDefinitionFilePath), JsonFile.new(applicationConfigFilePath))
    }
}