import Aggregate from '../../Core/Aggregate.mjs';
import OutboundAdapter from './OutboundAdapter.mjs';

export default class FluxAppApi {

  /**
   * @var {string}
   */
  #name;

  /**
   * @var {Aggregate}
   */
  #aggregate;

  /**
   * @var {OutboundAdapter}
   */
  #outbounds;
  #behaviors;


  /**
   * @return {FluxAppApi}
   */
  static async initialize(payload) {
    const obj = new this(
      payload
    )
    obj.#name = payload.name;
    obj.#outbounds = OutboundAdapter.new();
    obj.#behaviors =  await this.#outbounds.getApiBehaviors();
    obj.#initReactors();
    obj.#aggregate = Aggregate.initialize(payload,"flux-app/initialized");

    return obj;

  }

  constructor(payload) {




  }


  #initReactors() {
    const reactsOn =  this.#behaviors.reactsOn;
      Object.entries(reactsOn).forEach(([reactionId, reaction]) => {
        this.#outbounds.onRegister(  this.#name)(
          reaction.address,
          (message) => this.#reaction(reaction, message)
        );
      });
    }


  #reaction(reaction, message) {

    let payload = {};
    //todo find a better way for this
    if(reaction.hasOwnProperty('payload')) {
      payload = {
        ...reaction.payload,
      };
    } else {
      payload = {
        ...message.payload
      }
    }


    try {
      this.#aggregate[reaction.operationId](payload, reaction.replyTo);
    }
    catch (e) {
      console.error(reaction.operationId + " " + e)
    }
  }
}