import Aggregate from '../../Core/Aggregate.mjs';
import behaviors
  from '../../../behaviors/schemas/flux-layout-component.asyncapi.json' assert { type: 'json' };
import OutboundAdapter from './OutboundAdapter.mjs';

export default class FluxLayoutComponentApi {

  /**
   * @var {string}
   */
  #name;

  /**
   * @var {Aggregate}
   */
  #aggregate;
  #behaviors;
  /**
   * var {OutboundAdapter}
   */
  #outbounds;

  /**
   * @return {FluxLayoutComponentApi}
   */
  static async initialize(payload) {
    const obj = new this(payload);
    obj.#behaviors = behaviors;
    obj.#outbounds = OutboundAdapter.new();
    await obj.#initReactors();

    obj.#aggregate = await Aggregate.initialize(payload,
      obj.#name + '/initialized'
    )
    return obj;
  }

  constructor(payload) {
    this.#name = payload.name;
  }

  async #initReactors() {
    const reactsOn = this.#behaviors.reactsOn;
    Object.entries(reactsOn).forEach(([reactionId, reaction]) => {
      const reactionAddress = reaction.address.replace("{$name}", this.#name)

      if(reactionAddress.includes('{$slot}')) {
         /* this.#initSlotReactors(
            reactionAddress,
            reaction
          )*/
          return;
      }

      this.#outbounds.onRegister(this.#name)(
        reactionAddress,
        (message) => this.#reaction(reaction, message),
        true
      );
    });
  }


  async #reaction(reaction, message) {

    const payload = {
      ...reaction.payload,
      ...message.payload
    };
    try {
      const replyToAddress =  reaction.replyTo.replace("{$name}", this.#name)
      this.#aggregate[reaction.operationId](payload, replyToAddress);
    }
    catch (e) {
      console.error(reaction.operationId + " " + e)
    }
  }

  async #initSlotReactors(
    reactionAddress,
    reaction
  ) {
    const template = await this.#outbounds.loadTemplate(this.#name);
    const slots = await template.slots;
    if(slots) {
      Object.entries(slots).forEach(([slotName, slot]) => {
        const slottedReactionAddress = reactionAddress.replace("{$slot}", slotName)

        this.#outbounds.onRegister(this.#name)(
          slottedReactionAddress,
          (message) => this.#slotReaction(slotName, reaction, message),
          true
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
      const replyToAddress =  reaction.replyTo.replace("{$name}", this.#name)
      const slottedReplyToAddress = replyToAddress.replace("{$slot}", slotName)
      this.#aggregate[reaction.operationId](slotName, payload, slottedReplyToAddress);


    }
    catch (e) {
      console.error(reaction.operationId + " " + e)
    }
  }

}