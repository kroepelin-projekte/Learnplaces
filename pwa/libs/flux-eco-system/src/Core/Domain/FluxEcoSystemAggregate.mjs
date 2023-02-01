import {ChannelSubscription} from "./ValueObjects/ChannelSubscription.mjs";
import {DomainMessageName} from "./Messages/DomainMessageName.mjs";
import {ApplicationDefinition} from "./Definitions/ApplicationDefinition.mjs";
import {FluxEcoApplication} from "./FluxEcoApplication.mjs";

export class FluxEcoSystemAggregate {
    /**
     * @var {MessageRecorder}
     */
    #messageRecorder
    /**
     * @var {Map.<string,App>}
     */
    #applications;


    /**
     * @param {MessageRecorder} messageRecorder
     */
    constructor(messageRecorder) {
        this.#messageRecorder = messageRecorder;
        this.#applications = new Map();
    }

    /**
     * @param {MessageRecorder} messageRecorder
     * @returns {FluxEcoSystemAggregate}
     */
    static async new(messageRecorder) {
        return new this(messageRecorder);
    }

    /**
     * @param {ApplicationDefinition} applicationDefinition
     * @returns {Promise<void>}
     */
    async connectApp(applicationDefinition) {
        await this.#loadApplication(applicationDefinition);
        await this.#initOrbitalSubscriptions(this.#applications.get(applicationDefinition.id));
    }

    /**
     * @param {ApplicationDefinition} applicationDefinition
     * @returns {Promise<void>}
     */
    async #loadApplication(applicationDefinition) {
        const applicationApi = await ((await import('../../../../../' + applicationDefinition.apiFilePath)).Api.new());
        await this.#applyApplicationApiLoaded(FluxEcoApplication.new(applicationApi, applicationDefinition));
    }

    /**
     * @param {FluxEcoApplication} application
     * @returns {Promise<void>}
     */
    async #applyApplicationApiLoaded(application) {
        this.#applications.set(application.applicationDefinition.id, application);
    }


    /**
     * @param {FluxEcoApplication} application
     * @returns {Promise<void>}
     */
    async #initOrbitalSubscriptions(application) {
        for (const [channelName, receiveDefinition] of Object.entries(application.applicationDefinition.receives)) {
            const operationName = receiveDefinition.operationName;
            await this.#subscribeApplicationToChannel(ChannelSubscription.new(
                    receiveDefinition.channelName,
                    receiveDefinition.bindings,
                    (payload) => application.applicationApi.handle(operationName, payload)
                )
            )
        }
    }

    /**
     * @param {ChannelSubscription} channelSubscription
     * @returns {Promise<void>}
     */
    async #subscribeApplicationToChannel(channelSubscription) {
        await this.#applyApplicationSubscribedToChannel(
            channelSubscription
        ) ;
    }

    /**
     * @param {ChannelSubscription} channelSubscription
     * @returns {Promise<void>}
     */
    async #applyApplicationSubscribedToChannel(channelSubscription) {
        this.#messageRecorder.record(DomainMessageName.APPLICATION_SUBSCRIBED_TO_CHANNEL, channelSubscription);
    }
}