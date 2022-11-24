import Actor from "../../Core/Actor.mjs";
import MessageStream from '../../Adapters/MessageStream/MessageStream.mjs';
import Definitions from '../../Adapters/Definitions/Definitions.mjs';

export default class FluxLayoutApi {
  /** @var {string} */
  #applicationName;
  /** @var {string} */
  #actorName;
  /** @var {string} */
  #actorColor = '#420039'
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
    this.#applicationName = applicationName;
    this.#actorName = applicationName + "/" + "layout";
  }

  /**
   * @param {Config} config
   * @return {FluxLayoutApi}
   */
  static async initialize(config) {
    const obj = new this(config.applicationName);
    obj.#messageStream = await MessageStream.new(obj.#actorName, config.logEnabled, obj.#actorColor);
    await obj.#initDefinitions(config.definitionsBaseUrl);
    await obj.#initOperations();
    return obj;
  }

  async #initDefinitions(definitionsBaseUrl) {
    this.#definitions = await Definitions.new(definitionsBaseUrl)
  }

  async initActor() {
    this.#actor = await Actor.new(this.#applicationName, (name, payload) => {
        this.#publish(
          name,
          payload
        )
      },
      (templateId) => this.#definitions.template(templateId)
    );
  }

  async #initOperations() {
    const apiDefinition = await this.#definitions.apiDefinition();
    Object.entries(await apiDefinition.operations).forEach(([operationId, operation]) => {
      const addressDef = operation.on.address;
      const address = addressDef.replace('{$applicationName}', this.#applicationName);
      this.#messageStream.register(address, (payload) => this.#handle(operation.handles, payload))
    });
  }

  async #handle(command, payload) {
    try {
      this.#actor[command](payload);
    }
    catch (e) {
      console.error(command + " " + e)
    }
  }


  async #publish(
    publishTo, payload
  ) {
    this.#messageStream.publish(publishTo, payload)
  }

}