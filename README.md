Amos CWH
========

The module Collaboration Web House defines the rules for collaboration between project plugins and its network.  
The purpose of cwh is to enable publication of contents using rules, based on user interests or network membership.

#### Content Models ####
For content model we mean a model containing information that can be published to all users, shared in a network or marked as belonging to specif interest areas (tags).  
The interface that must be implemented to activate the model with cwh is (from amos core) **open20\amos\core\interfaces\ContentModelInterface** (see section **Configuration Wizard**)  
Content model configurations are stored in table **cwh_config_contents**.  
##### Publication #####
Once created, the content publication is stored in table **cwh_pubblicazioni** 
The News model in AmosNews is an example of content model.

#### Cwh nodes ####

Cwh nodes are domains for publication, as validation scope or as recipients (publication scope) - see validators and recipients section .  
Domain configurations are stored in table **cwh_config**, from this configuration the view **'cwh_nodi_view'** is created, to map information about all the networks present in the project (see section Cwh Nodi View).  

##### Network Models #####
A network is a model that allows creation of membership groups (user-network association) and information sharing between users belonging to the same group.  
The interface that a model must implement to be suitable as a cwh publication network is (in amos cwh) **open20\amos\cwh\base\ModelNetworkInterface** (see section **Configuration Wizard**)  
In my profile, it is possible to view user networks. 

##### User Domain #####  
In addition to networks models, in **cwh_config** table we can find configuration for single user, that is not to be considered properly as a network but has its own configurations and takes part to cwh processes as a node (a content can be published for a single user).
In amos admin my profile in network section, UserContact map is displayed as network (if user contacts are enabled) but it does not depend on cwh configuration: user contacts can be enabled even if cwh module is not enabled.
User nodes can be set as Validator (Publishing for fields in content form) but not as publication scope (recipient) as by now it not possible to publish a content visible just to another user.


#### Publication Rules ####
Default publication rules are:
1. **PUBLIC** To All - public for all users
2. **TAG** All users in the interest areas 
   - in tab 'own interest' the content is visible only if user interest matches content tags
   - visible in tab 'all'
3. **NETWORK** All users in specific scopes: it means the content has been published within one or more networks
    - in tab 'own interest' the content is visible only to user who are member of the specified networks
    - in tab 'all' the content is visible if the network is public (eg. community open), if the network has no visibility rules or if the user is member of the network
4. **NETWORK AND TAGS** All users in specified networks and matching the content tags.
    - Same as 3. with addinitional condition of matching areas of interest to view the content in 'own interest' tab

In in content model with **CwhNetworkBehaviors** 
* the attribute specifying the publication rule is **$model->regola_pubblicazione**
* in model form the field to set publication rule is shown by **widget open20\amos\cwh\widgets\RegolaPubblicazione**.
 
See section **Content publication** for more information.

##### Publication rule Customization ##### 
Rule 2 and 4, based on tags are disabled if the tag module is not active (not present in project configuration).  
It is possible to allow publishing with role 1 (to all users) only users having a specific role (rbac); to do so add to Cwh module configurations: 

```php
'modules' => [
    ....
    'cwh' => [
            'class' => 'open20\amos\cwh\AmosCwh',
            'regolaPubblicazioneFilter' => true,
            'regolaPubblicazioneFilterRole' => 'MY_ROLE'
        ],
    ....
]
```
By default the publication rules filtering by role is disabled (all users can publish contents with rule 1.
If publication rule filtering is enabled but role is not specified, the default role allowing publishing rule 1 is 'VALIDATOR_PLUS'

To disable publication rules, add to cwh configurations regolaPubblicazioneEnabled set to false:
```php
'modules' => [
    ....
    'cwh' => [
            'class' => 'open20\amos\cwh\AmosCwh',
            'regolaPubblicazioneEnabled' => false
        ],
    ....
]
```

#### Validators - 'Publishing for'  ####
If content model workflow is not bypassed, it is mandatory to set a validation scope when publishing a content.  
Therefore in form/wizard the cwh node must be specified independently from publication rule.

In in content model with **CwhNetworkBehaviors** 
* the attribute specifying the validators is **$model->validatori** (an array in case in future more validators will needed).
* in model form the field to set validators is shown by **widget open20\amos\cwh\widgets\Validatori**.

If 'My own' (Mio conto) is selected, the user is personally publishing a content (no specific network is selected) so validation may be done by users with 'VALIDATOR' role or by the user facilitator (if it is enabled in the platform). 
If a network validation scope is set, the validation will be done by users having permission to validate the content under the network: **domains permission to create and validate contents in a network** are stored in table **cwh_auth_assignment**.

Examples:  

a. Jhon Doe, user_id = 5 is publishing a News with Personal Validation scope (My own) and he is falicilitated by user with id 3. 
 News can be validated by :
 - Jhon himself if he has VALIDATOR role.
 - a VALIDATOR user.
 - user 3 (not needed to be a VALIDATOR too) because he has cwh_auth_assignment permission to validate news in user domain for user with id= 3.  
 
b. Jhon Doe wants to create a news in community with id = 6.
 - he must be a participant of community 6 and have cwh_auth_assignment permission to create news for community with id = 6 (PARTICIPANT role in community_user_mm gives the permission)
 - the news can be validated by users having validate permission for community 6 - usually the community managers of the community.
 - a VALIDATOR user can't validate the news if he is not member of community 6.
 network (eg. in a community any user of the community with PARTICIPANT role in the COMMUNITY_USER_MM table).
 
##### Validators Customization ##### 
It is possible to hide validator section from form/wizard (needed for example in case workflow is not active), setting option validatoriEnabled to false:
```php
'modules' => [
    ....
    'cwh' => [
            'class' => 'open20\amos\cwh\AmosCwh',
            'validatoriEnabled' => false
        ],
    ....
]
```

#### Recipients - 'Publication scope'  ####
If a publication rule that involve networks has been choosed, it is mandatory to select a network for for content publicatio (eg. a community).

In in content model with **CwhNetworkBehaviors** 
* the attribute specifying the publication scope is **$model->destinatari** (an array containing cwh_nodi_id of the selected networks).
* in model form the field to set recipients is shown by **widget open20\amos\cwh\widgets\Destinatari**.

##### Recipients Customization ##### 
It is possible to hide recipients section from form/wizard (needed for example in case workflow is not active), setting option destinatariEnabled to false:
```php
'modules' => [
    ....
    'cwh' => [
            'class' => 'open20\amos\cwh\AmosCwh',
            'destinatariEnabled' => false
        ],
    ....
]
```

#### Recipients Check ####
Cwh module also provides a widget to get the 'Recipients check' utility inside the content publication form.
Recipients check **widget open20\amos\cwh\widgets\RecipientsCheck** draws a button opening a modal showing the list of user for whose the content will be of 'own interest'.

     
#### Configuration Wizard ####

To use the configuration wizard, check that php extensions soap and intl are enabled.
##### Networks

##### Contents
It is possible to configure a content model if it is suitable to be used with cwh content management ( implements **open20\amos\core\interfaces\ContentModelInterface** ).
To do so:
- Activate cwh plugin
- Open cwh configuration wizard (admin privilege is required) url: <yourPlatformurl>/cwh/configuration/wizard
- search for the model in content configuration section
- edit configuration of the model and save
**//TODO specify how fields are used**

## Documentation

[Read the Documentation](docs/guide-it/README.md)

## Installation


```php
'modules' => [
    ....
    'cwh' => [
            'class' => 'open20\amos\cwh\AmosCwh',
        ],
    ....
]
```

#### Module Parameters ####
**regolaPubblicazioneFilter** - see section Publication rule customization  
**regolaPubblicazioneFilterRole** - see section Publication rule customization  
**regolaPubblicazioneEnabled** - see section Publication rule customization  
**validatoriEnabled** - see section Validators customization  
**destinatariEnabled** - see section Recispients customization  
**cached** - bool default = false - if enable cwh query caching when finding contents  
**cacheDuration** int default = 86400 - if query caching is active (cached = true) the cache duration (by default 24 hours).  
**enableDestinatariFatherChildren** : if is true you can publish a content from a community to the community father and the community children.
**tagsMatchEachTree**  : bool - default = false.  
Set to true if a content is to be considered of user interest when there is tag-match of each tag tree  
Default is false: at least one content tag matching user interest (any tree)
