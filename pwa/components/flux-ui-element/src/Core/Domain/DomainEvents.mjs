export class DomainEvent {
  /** @var {string} */
  name = "";
}

export class Created extends DomainEvent {
  static new(appId) {
    return { appId: appId, name: "created" }
  }
}

export class ContextInitialized extends DomainEvent {
  static new(contextId, srcApi) {
    return {
      name: "contextInitialized",
      contextId: contextId,
      srcApi: srcApi
    }
  }

}