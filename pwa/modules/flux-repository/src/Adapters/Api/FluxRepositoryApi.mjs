export default class FluxRepositoryApi {

  /**
   * @var {Aggregate}
   */
  #aggregate;

  /**
   * @return {FluxRepositoryApi}
   */
  static initialize() {
    return new this();
  }

  constructor() {
   // this.#initReactors();
   // this.#initAggregate();
  }

  #initAggregate() {
   // this.#aggregate = Aggregate.create(FluxBroadcastChannelApi.newDomainMessagePublisher(true), "flux-data-fetcher/created");
  }

  #initReactors() {
    /*
    const reactsOn = asyncapi.reactsOn;

    Object.entries(reactsOn).forEach(([reactionId, reaction]) => {
      FluxBroadcastChannelApi.registerReactor(
        reaction.address,
        (message) => this.#reaction(reaction,message),
        true
      );
    });
    */

  }

  #reaction(reaction, message) {

    const payload = {
      ... reaction.payload,
      ... message.payload
    };

    this.#aggregate[reaction.operationId](payload, reaction.replyTo);
  }


}