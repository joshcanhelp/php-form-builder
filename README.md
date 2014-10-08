# PHP Form Builder by JoshCanHelp

This is a small PHP class that makes it easy to build and output forms as HTML or XHTML. Forms are tedious and can be difficult to build just right. Also, there are so many different option possible that it's easy to forget what you can do with them. 

I tried to balance ease-of-use with flexibility and came up with something I find pretty darn helpful. I'm considering this a "beta" for the time being since it's only being used in a few applications and all the different options have not been exhaustively checked. 

Give it a try and let me know what you like, hate, and think needs to be fixed. 

## Working with the form builder

The process for creating a form is simple:

1. Instantiate the class
2. Change any form attributes, if desired
3. Add inputs, in order you want to see them
4. Output the form

Let's walk through these one by one

### 1) Instantiate the class

This is pretty simple:

```php
$new_form = new PhpFormBuilder();
```

This uses all the default settings for the form, which are as follows:

* `action: empty, submit to current URL`
* `method: post`
* `enctype: application/x-www-form-urlencoded`
* `class: none`
* `id: none`
* `markup: html`
* `novalidate: false`
* `add_nonce: false`
* `add_honeypot: true`
* `form_element: true`
* `add_submit: true`

Explanations for each of the settings are below

You can also instantiate by passing in a URL, which becomes the action for the form:

```php
$new_form = new PhpFormBuilder('http://submit-here.com');
```

### 2) Change any form attributes, if desired

Once the form has been created, use the <code>set_att</code> function to change the default attributes:

```php
// Add a new form action
$new_form->set_att('action', 'http://submit-here.com');

// Change the submit method
$new_form->set_att('method', 'get');

// Change the enctype
$new_form->set_att('enctype', 'multipart/form-data');

// Can be set to 'html' or 'xhtml'
$new_form->set_att('markup', 'xhtml');

// Classes are added as an array
$new_form->set_att('class', array());

// Add an id to the form
$new_form->set_att('id', 'xhtml');

// Adds the HTML5 "novalidate" attribute
$new_form->set_att('novalidate', true);

// Adds a WordPress nonce field using the string being passed
$new_form->set_att('add_nonce', 'build_a_nonce_using_this');

// Adds a blank, hidden text field for spam control
$new_form->set_att('add_honeypot', true);

// Wraps the inputs with a form element
$new_form->set_att('form_element', true);

// If no submit type is added, add one automatically
$new_form->set_att('form_element', true);
```

Currently, there are some restrictions to what can be added but no check as to whether the classes or ids are valid so be mindful of that. 

### 3) Add inputs, in order you want to see them

Inputs can be added one at a time or as a group. Either way, the order they are added is the order in which they'll show up.

Add fields using their label (in human-readable form), an array of settings, and a name/id slug, if needed.

```php
$new_form->add_input('I am a little field', array(), 'little_field')
```

* Argument 1: A human-readable label that is parsed and turned into the name and id, if these options aren't explicitly set. If you use a simple label like "email" here, make sure to set a more specific name in argument 3.
* Argument 2: An array of settings that are merged with the default settings to control the display and type of field. See below for default and potential settings here. 
* Argument 3: A string, valid for an HTML attribute, used as the name and id. This lets you set specific submission names that differ from the field label. 

Default and possible settings for field inputs (argument 2):

<code>type</code>

* Default is "text"
* Can be set to anything and, unless mentioned below, is used as the "type" for an input field
* Setting this to "title" will output an h3 element using the label text
* Setting this to "textarea" will build a text area field
* Using "select" in combination with the "options" argument will create a dropdown. 

<code>name</code>

* Default is argument 3, if set, or the label text formatted
* This becomes the "name" attribute on the field

<code>id</code>

* Default is argument 3, if set, or the label text formatted
* This becomes the "id" attribute on the field and the "for" attribute on the label

<code>label</code>

* Default is argument 1, can be set explicitly using this argument

<code>value</code>

* Default is empty
* If a $_REQUEST index is found with the same name, the value is replaced with that value found

<code>placeholder</code>

* Default is empty
* HTML5 attribute to show text that disappears on field focus

<code>class</code>

* Default is an empty array
* Add multiple classes using an array of valid class names

<code>options</code>

* Default is an empty array
* The options array is used for fields of type "select," "checkbox," and "radio." For other inputs, this argument is ignored
* The array should be an associative array with the value as the key and the label name as the value like <code>array('value' => 'Name to show')</code>
* The label name for the field is used as a header for the multiple options (set "add_label" to "false" to suppress)

<code>min</code>

* Default is empty
* Used for types "range" and "number"

<code>max</code>

* Default is empty
* Used for types "range" and "number"

<code>step</code>

* Default is empty
* Used for types "range" and "number"

<code>autofocus</code>

* Default is "false"
* A "true" value simply adds the HTML5 "autofocus" attribute

<code>checked</code>

* Default is "false"
* A "true" value simply adds the "checked" attribute

<code>required</code>

* Default is "false"
* A "true" value simply adds the HTML5 "required" attribute

<code>add_label</code>

* Default is "true"
* A "false" value will suppress the label for this field

<code>wrap_tag</code>

* Default is "div"
* A valid HTML tag name for the field wrapper. 
* Set this to an empty string to not use a wrapper for the field

<code>wrap_class</code>

* Default is an array with "form_field_wrap" as the only value
* Classes should be added as an array of valid HTML class names

<code>wrap_id</code>

* Default is empty
* Add an id to this field by passing a string

<code>wrap_style</code>

* Default is empty
* This string of text will be added within a style attribute

### 4) Output the form

One quick statement outputs the form as HTML:

```php
$new_form->build_form();
```

## Roadmap

There are a few things that I'd like to correct here and a few features to add. In order of priority:

* Validation for adding classes and ids
* Add fieldsets and legends
* Function to change the default field settings
* Add ability to set selected and checked for select and multiple checkboxes
* More strict name generation
* Ability to add HTML within the form
* 'html_before' and 'html_after' for form attributes

