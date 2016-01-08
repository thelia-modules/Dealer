<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/
/*************************************************************************************/
namespace Dealer\Event;
/**
 * Class DealerEvents
 */
class DealerEvents
{

    // DEALER
    const DEALER_CREATE = "dealer.dealer.create";
    const DEALER_CREATE_BEFORE = "dealer.dealer.create.before";
    const DEALER_CREATE_AFTER = "dealer.dealer.create.after";
    const DEALER_UPDATE = "dealer.dealer.update";
    const DEALER_UPDATE_BEFORE = "dealer.dealer.update.before";
    const DEALER_UPDATE_AFTER = "dealer.dealer.update.after";
    const DEALER_DELETE = "dealer.dealer.delete";
    const DEALER_DELETE_BEFORE = "dealer.dealer.delete.before";
    const DEALER_DELETE_AFTER = "dealer.dealer.delete.after";

    //DEALER CONTACT
    const DEALER_CONTACT_CREATE = "dealer.dealer-contact.create";
    const DEALER_CONTACT_CREATE_BEFORE = "dealer.dealer-contact.create.before";
    const DEALER_CONTACT_CREATE_AFTER = "dealer.dealer-contact.create.after";
    const DEALER_CONTACT_UPDATE = "dealer.dealer-contact.update";
    const DEALER_CONTACT_UPDATE_BEFORE = "dealer.dealer-contact.update.before";
    const DEALER_CONTACT_UPDATE_AFTER = "dealer.dealer-contact.update.after";
    const DEALER_CONTACT_DELETE = "dealer.dealer-contact.delete";
    const DEALER_CONTACT_DELETE_BEFORE = "dealer.dealer-contact.delete.before";
    const DEALER_CONTACT_DELETE_AFTER = "dealer.dealer-contact.delete.after";

    //DEALER CONTACT INFO
    const DEALER_CONTACT_INFO_CREATE = "dealer.dealer-contact-info.create";
    const DEALER_CONTACT_INFO_CREATE_BEFORE = "dealer.dealer-contact-info.create.before";
    const DEALER_CONTACT_INFO_CREATE_AFTER = "dealer.dealer-contact-info.create.after";
    const DEALER_CONTACT_INFO_UPDATE = "dealer.dealer-contact-info.update";
    const DEALER_CONTACT_INFO_UPDATE_BEFORE = "dealer.dealer-contact-info.update.before";
    const DEALER_CONTACT_INFO_UPDATE_AFTER = "dealer.dealer-contact-info.update.after";
    const DEALER_CONTACT_INFO_DELETE = "dealer.dealer-contact-info.delete";
    const DEALER_CONTACT_INFO_DELETE_BEFORE = "dealer.dealer-contact-info.delete.before";
    const DEALER_CONTACT_INFO_DELETE_AFTER = "dealer.dealer-contact-info.delete.after";

    //DEALER Schedules
    const DEALER_SCHEDULES_CREATE = "dealer.dealer-schedules.create";
    const DEALER_SCHEDULES_CREATE_BEFORE = "dealer.dealer-schedules.create.before";
    const DEALER_SCHEDULES_CREATE_AFTER = "dealer.dealer-schedules.create.after";
    const DEALER_SCHEDULES_UPDATE = "dealer.dealer-schedules.update";
    const DEALER_SCHEDULES_UPDATE_BEFORE = "dealer.dealer-schedules.update.before";
    const DEALER_SCHEDULES_UPDATE_AFTER = "dealer.dealer-schedules.update.after";
    const DEALER_SCHEDULES_DELETE = "dealer.dealer-schedules.delete";
    const DEALER_SCHEDULES_DELETE_BEFORE = "dealer.dealer-schedules.delete.before";
    const DEALER_SCHEDULES_DELETE_AFTER = "dealer.dealer-schedules.delete.after";

    //DEALER Content
    const DEALER_CONTENT_LINK_CREATE = "dealer.content.link.create";
    const DEALER_CONTENT_LINK_CREATE_BEFORE = "dealer.content.link.create.before";
    const DEALER_CONTENT_LINK_CREATE_AFTER = "dealer.content.link.create.after";
    const DEALER_CONTENT_LINK_UPDATE = "dealer.content.link.update";
    const DEALER_CONTENT_LINK_UPDATE_BEFORE = "dealer.content.link.update.before";
    const DEALER_CONTENT_LINK_UPDATE_AFTER = "dealer.content.link.update.after";
    const DEALER_CONTENT_LINK_DELETE = "dealer.content.link.delete";
    const DEALER_CONTENT_LINK_DELETE_BEFORE = "dealer.content.link.delete.before";
    const DEALER_CONTENT_LINK_DELETE_AFTER = "dealer.content.link.delete.after";

    //DEALER Folder
    const DEALER_FOLDER_LINK_CREATE = "dealer.folder.link.create";
    const DEALER_FOLDER_LINK_CREATE_BEFORE = "dealer.folder.link.create.before";
    const DEALER_FOLDER_LINK_CREATE_AFTER = "dealer.folder.link.create.after";
    const DEALER_FOLDER_LINK_UPDATE = "dealer.folder.link.update";
    const DEALER_FOLDER_LINK_UPDATE_BEFORE = "dealer.folder.link.update.before";
    const DEALER_FOLDER_LINK_UPDATE_AFTER = "dealer.folder.link.update.after";
    const DEALER_FOLDER_LINK_DELETE = "dealer.folder.link.delete";
    const DEALER_FOLDER_LINK_DELETE_BEFORE = "dealer.folder.link.delete.before";
    const DEALER_FOLDER_LINK_DELETE_AFTER = "dealer.folder.link.delete.after";

    //DEALER Brand
    const DEALER_BRAND_LINK_CREATE = "dealer.brand.link.create";
    const DEALER_BRAND_LINK_CREATE_BEFORE = "dealer.brand.link.create.before";
    const DEALER_BRAND_LINK_CREATE_AFTER = "dealer.brand.link.create.after";
    const DEALER_BRAND_LINK_UPDATE = "dealer.brand.link.update";
    const DEALER_BRAND_LINK_UPDATE_BEFORE = "dealer.brand.link.update.before";
    const DEALER_BRAND_LINK_UPDATE_AFTER = "dealer.brand.link.update.after";
    const DEALER_BRAND_LINK_DELETE = "dealer.brand.link.delete";
    const DEALER_BRAND_LINK_DELETE_BEFORE = "dealer.brand.link.delete.before";
    const DEALER_BRAND_LINK_DELETE_AFTER = "dealer.brand.link.delete.after";
}