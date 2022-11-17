import OutboundAdapter from './OutboundAdapter.mjs';
import Aggregate from "../../Core/Aggregate.mjs";

export default class FluxLayoutComponentApi {

    /** @var {Aggregate} */
    #aggregate;

    #apiBehaviors;
    /** @var {OutboundAdapter} */
    #outbounds;

    /**
     * @return {FluxLayoutComponentApi}
     * @param {InitializeFluxLayoutComponent} initializeFluxLayoutComponent
     */
    static async initialize(initializeFluxLayoutComponent) {
        const obj = new this();
        obj.#outbounds = OutboundAdapter.initialize(initializeFluxLayoutComponent.payload);
        obj.#apiBehaviors = await obj.#outbounds.getApiBehaviors();
        obj.#aggregate = await Aggregate.initialize(obj.#outbounds);
        await obj.#initGeneralReactors();
        return obj;
    }

    constructor() { }

    async #initGeneralReactors() {
        Object.entries(this.#apiBehaviors.reactsOn).forEach(([reactionId, reaction]) => {

            console.log(reaction);

            if (reaction.address.includes('{name}')) {
                return;
            }
            if (reaction.address.includes('{$slot}')) {
                return;
            }

            this.#outbounds.onRegister(this.#aggregate.name)(
                reaction.address,
                (message) => this.#reaction(reaction, message),
                true
            );
        });
    }

    async #initNamedReactors() {
        const reactsOn = this.#apiBehaviors.reactsOn;
        Object.entries(reactsOn).forEach(([reactionId, reaction]) => {
            if (reactionAddress.includes('{$name}') === false) {
                return;
            }
            const reactionAddress = reaction.address.replace("{$name}", this.#aggregate.name)
            this.#outbounds.onRegister(this.#aggregate.name)(
                reactionAddress,
                (messagePayload) => this.#reaction(reaction, messagePayload),
                true
            );
        });
    }


    async #reaction(reaction, messagePayload) {

        const payload = {
            ...reaction.payload,
            ...messagePayload
        };
        try {
            //const replyToAddress = reaction.replyTo.replace("{$name}", this.#name)
            this.#aggregate[reaction.operationId](messagePayload);
        } catch (e) {
            console.log(this.#aggregate);
            console.error(reaction.operationId + " " + e)
        }
    }

    async #initSlotReactors(
        reactionAddress,
        reaction
    ) {
        const template = await this.#outbounds.loadTemplate(this.#aggregate.name);
        const slots = await template.slots;
        if (slots) {
            Object.entries(slots).forEach(([slotName, slot]) => {
                const slottedReactionAddress = reactionAddress.replace("{$slot}", slotName)

                this.#outbounds.onRegister(this.#aggregate.name)(
                    slottedReactionAddress,
                    (message) => this.#slotReaction(slotName, reaction, message)
                );
            });
        }
    }

    async #slotReaction(slotName, reaction, message) {

        const payload = {
            ...reaction.payload,
            ...message.payload
        };
        try {
            const replyToAddress = reaction.replyTo.replace("{$name}",this.#aggregate.name)
            const slottedReplyToAddress = replyToAddress.replace("{$slot}", slotName)
            this.#aggregate[reaction.operationId](slotName, payload, slottedReplyToAddress);


        } catch (e) {
            console.error(reaction.operationId + " " + e)
        }
    }

}