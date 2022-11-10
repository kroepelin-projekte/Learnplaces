import Aggregate from '../../Core/Aggregate.mjs';
import OutboundAdapter from './OutboundAdapter.mjs';
import behaviors from '../../../../../behaviors/schemas/flux-app.asyncapi.json' assert { type: 'json' };

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
    return new this(
      payload
    )
  }

  constructor(payload) {

    this.#name = payload.name;
    this.#behaviors = behaviors;
    this.#outbounds = OutboundAdapter.new();
    this.#initReactors();
    this.#aggregate = Aggregate.initialize(payload,"flux-app/initialized");
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