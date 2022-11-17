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
  #apiBehaviors;
  /**
   * var {OutboundAdapter}
   */
  #outbounds;

  /**
   * @return {FluxLayoutComponentApi}
   * @param {InitializeFluxRepository} initializeFluxRepository
   */
  static async initialize(initializeFluxRepository) {
    const obj = new this();
    obj.#outbounds = OutboundAdapter.initialize(initializeFluxRepository.payload);
    obj.#apiBehaviors = await obj.#outbounds.getApiBehaviors();
    obj.#aggregate = await Aggregate.initialize(obj.#outbounds);
    await obj.#initReactors();
    return obj;
  }

  constructor() {

  }

  async #initReactors() {
    Object.entries(this.#apiBehaviors.reactsOn).forEach(([reactionId, reaction]) => {
      console.log(reaction);
      this.#outbounds.onRegister(this.#aggregate.name)(
        reaction.address,
        (message) => this.#reaction(reaction, message),
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
      this.#aggregate[reaction.operationId](messagePayload);
    }
    catch (e) {
      console.error(reaction.operationId + " " + e)
    }
  }

}