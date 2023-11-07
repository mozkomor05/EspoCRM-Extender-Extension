# EspoCRM View Extender

View Extender is a tool for EspoCRM developers, which allows to extend any (core) view and its derived views.

<!-- TABLE OF CONTENTS -->
<details>
<summary>Table of Contents</summary>
<ol>
<li>
<a href="#usage">Usage</a>
<ul>
<li><a href="#example">Example</a></li>
</ul>
</li>
<li>
<a href="#features">Features</a>
</li>
<li>
<a href="#acknowledgements">Acknowledgements</a>
</li>
</ol>
</details>

## Usage

1. Define the view extension mapping in `app.client.viewExtensions` metadata.
2. Create the view extension file and extend the view using the `extend` function or modern ESM syntax (experimental).

`extend` signatures:

```typescript
function extend(callback: (dep: Dep) => unknown): void;
function extend(dependencyList: string[], callback: (dep: Dep) => unknown): void;
function extend(id: string, dependencyList: string[], callback: (dep: Dep) => unknown): void;
```

### Example

`custom/Espo/Custom/Resources/metadata/app/client.json`

```json
{
    ...
    "viewExtensions": {
        "views/detail": [
            "__APPEND__",
            "custom:extensions/view/detail"
        ],
        ...
    }
}
```

`client/custom/src/extensions/view/detail.js`o

```js
extend(Dep => {
    return class extends Dep {
        setup() {
            super.setup();

            console.log('Hello World!');
        }
    };
});
```

or experimental ESM synax:

```js
import DetailView from 'views/detail';

export default class extends DetailView {
    setup() {
        super.setup();

        console.log('Hello World!');
    }
};
```

## Features

- ESM syntax support
- Multiple non-conflicting extensions
- Works with bundles (introduced in v8.0)

## Acknowledgements

A big thanks to @eymen-elkum, whose great extensions inspired me to create this tool.
