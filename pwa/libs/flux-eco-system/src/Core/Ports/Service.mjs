import {MessageRecorder} from "../Domain/MessageRecorder.mjs";
import {Outbounds} from "./Outbounds.mjs";
import {FluxEcoSystemAggregate} from "../Domain/FluxEcoSystemAggregate.mjs";
import {ApplicationDefinition} from "../Domain/Definitions/ApplicationDefinition.mjs";
import {DomainMessageDispatcher} from "./DomainMessageDispatcher.mjs";

export class Service {
    /**
     * @var {DomainMessageDispatcher}
     */
    #domainMessageDispatcher;
    /**
     * @var {MessageRecorder}
     */
    #recorder;
    /**
     * @var {FluxEcoSystemAggregate}
     */
    #aggregate;

    /**
     *
     * @param DomainMessageDispatcher
     * @param recorder
     */
    constructor(domainMessageDispatcher, recorder) {
        this.#domainMessageDispatcher = domainMessageDispatcher;
        this.#recorder = recorder

        this.#aggregate = FluxEcoSystemAggregate.new(
            recorder
        )
    }

    /**
     * @param {DomainMessageDispatcher} domainMessageDispatcher
     * @returns {Service}
     */
    static new(domainMessageDispatcher) {
        return new this(domainMessageDispatcher, MessageRecorder.new());
    }

    /**
     *
     * @param {JsonFile} appDefinitionJsonFile
     * @param {JsonFile} appConfigJsonFile
     * @returns {Promise<void>}
     */
    async connectApplication(appDefinitionJsonFile, appConfigJsonFile) {
        const appDefinition = await ApplicationDefinition.fromJson(await appDefinitionJsonFile.toJson(), await appConfigJsonFile.toJson());

        await this.#aggregate.connectApp(appDefinition);

        if (this.#recorder.recordedMessages.size > 0) {
            this.#recorder.recordedMessages.forEach((domainMessage, messageName) => {
                    this.#domainMessageDispatcher[messageName](domainMessage)
                }
            );
        }
        this.#recorder.flush();
    }

}