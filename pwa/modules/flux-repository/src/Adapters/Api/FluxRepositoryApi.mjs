import behaviors
  from '../../../behaviors/schemas/flux-repository.asyncapi.json' assert { type: 'json' };
import OutboundAdapter
  from './OutboundAdapter.mjs';
import Aggregate from '../../Core/Aggregate.mjs';

export default class FluxRepositoryApi {
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
      obj.#name + '/repository/initialized'
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

}