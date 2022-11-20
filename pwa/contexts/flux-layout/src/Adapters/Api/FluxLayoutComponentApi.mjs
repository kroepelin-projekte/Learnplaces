import Actor from "../../Core/Actor.mjs";
import MessageStream from '../../Adapters/MessageStream/MessageStream.mjs';
import Definitions from '../../Adapters/Definitions/Definitions.mjs';

export default class FluxLayoutComponentApi {
  /** @var {string} */
  #actorName;
  /** @var {Actor} */
  #actor;
  /** @var {MessageStream} */
  #messageStream;
  /** @var {Definitions} */
  #definitions;

  /**
   * @private
   * @param applicationName
   */
  constructor(applicationName) {
    this.#actorName = applicationName + "/" + "layout";
  }

  /**
   * @return {FluxLayoutComponentApi}
   */
  static async initialize(applicationName, logEnabled) {
    const obj = new this(applicationName);
    obj.#messageStream = await MessageStream.new(obj.#actorName, logEnabled);
    await obj.#initDefinitions();
    await obj.#initReactors();
    await obj.#initActor()
  }

  async #initDefinitions() {
    this.#definitions = await Definitions.new(await document.getElementById("flux-pwa-base").href)
  }

  async #initActor() {
    this.#actor = await Actor.new(this.#actorName, (name, payload) => {
        this.#publish(
          name,
          payload
        )
      },
      (templateId) => this.#definitions.template(templateId)
    );
  }

  async #initReactors() {
    const apiDefinition = await this.#definitions.apiDefinition();
    Object.entries(await apiDefinition.reactions).forEach(([reactionId, reaction]) => {
      const addressDef = reaction.onMessage;
      const address = addressDef.replace('{$actorName}', this.#actorName);
      this.#messageStream.register(address, (payload) => this.#reaction(reaction.process, payload))
    });
  }

  async #reaction(process, payload) {
    try {
      this.#actor[process](payload);
    }
    catch (e) {
      console.error(process + " " + e)
    }
  }


  async #publish(
    publishAddress, payload
  ) {
    this.#messageStream.publish(publishAddress, payload)
  }

}