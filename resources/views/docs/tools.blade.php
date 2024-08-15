@extends('layouts.docs')
@section('title',__('Telegram'))
@section('content')
<div class="main-content">
  <section class="section">
    <div class="section-body">
      <ul id="submenu">
        <li><a href="#search">{{ __("Create Telegram Bot") }}</a></li>
        <li><a href="#history">{{ __("Integrate Telegram Bot With Telegram Group") }}</a></li>
        <li><a href="#chat">{{ __("Filter Message") }}</a></li>
        <li><a href="#subscriber">{{ __("Group Subscriber Manager") }}</a></li>
        <li><a href="#msg_campaign">{{ __("Message Campaign") }}</a></li>
      </ul>
      <div class="section-header text-center">
        <h1 class="main-header">{{ __("Telegram") }} </h1>
      </div>
      <hr  class='main-hr'/>
      
      <div class="section-header">
        <h1 id="search">{{ __("Create Telegram Bot") }}</h1>
      </div>
      <hr />
      <p>{{__("Throughout this tutorial, we will cover the essential steps involved in creating a Telegram bot, including setting up a bot account, obtaining an API token, configuring basic functionalities, and understanding the Bot API. We will then delve into the integration process with :appname, exploring its capabilities and benefits in streamlining your bot development journey.",['appname'=>config('app.name')])}}</p>

      <p><b>{{__("Quick Step to Create a Telegram Bot")}}</b></p>
      <ul>
        <li>{{__('Open the Telegram app and search for the `BotFather` bot.')}}</li>
        <li>{{__('Start a chat with the BotFather by sending the command `/start`.')}}</li>
        <li>{{__('To create a new bot, use the `/newbot` command. The BotFather will guide you through the process and ask you to choose a name and a username for your bot.')}}</li>
        <li>{{__('After successfully creating the bot, the BotFather will provide you with a token. This token is essential as it serves as an API key for your bot to interact with the Telegram API.')}}</li>
      </ul>
      


      <div class="section-header">
        <h1 id="history">{{ __("Integrate Telegram Bot with :appname",['appname'=>config('app.name')]) }}</h1>
      </div>
      <hr />
      <p> {{ __("In this step-by-step guide, we will walk through the process of creating a Telegram bot and seamlessly integrating it into an existing Telegram bot. By following these instructions, you will be able to set up your own Telegram bot and leverage its capabilities within the Telegram platform.") }}
      </p>
      <p><b>{{__('Step 1')}}: {{__('Create a Telegram Group')}}</b></p>
      <p>{{__('Once you have created the bot and connected it to this application, creating a group is the next step. However, if you already have a group, you can simply skip this step.')}}</p>

      <p><b>[{{__('By Using Phone App')}}]</b></p>
      <p>{{__('To create a group from the phone app click on the edit button from the Chats and click on New Group option. Give a name to the group and click on the create button.')}}</p>

      <img src="{{asset('assets/docs/images/group_in_mobile.jpeg')}}" class="img-fluid img-thumbnail"/>

      <p><b>[{{__('By using Desktop App')}}]</b></p>
      <p>{{__('To create a group from the desktop app, click on the Hamburger button then go to New Group. Give a name to the group and click next. You can add members from the contact list if you want. Finally, click on the create button.')}}</p>
      <img src="{{asset('assets/docs/images/group_in_desktop.png')}}" class="img-fluid img-thumbnail"/>

      <p><b>{{__('Step 2')}}: {{__('Add the bot to the group and make the bot Admin')}}</b></p>
      <p>{{__('To add the bot to the group and make it an admin, you can do so from both the phone app and desktop app.')}}</p>

      <p><b>[{{__('By Using Phone App')}}]</b></p>
      <p>{{__('From the group, click on the top of the group to get the settings. Then click on the “Add members” button. Search for the bot account and select it. Then click on the done')}}</p>
      <img src="{{asset('assets/docs/images/admin_in_phone.jpeg')}}" class="img-fluid img-thumbnail"/>
      <p>{{__('After you have added the bot to the group as member now, you need to make the bot as admin.')}}</p>
      <p>{{__('To make the bot admin of the group from the phone app, go to the group and click on the edit button first. Then go to administration and click on the “Add admin” button. This will show the members of the group. Select the bot account, review the permissions the bot will get. Finally, click on the “Done” button.')}}</p>
      <img src="{{asset('assets/docs/images/admin_in_phone2.jpeg')}}" class="img-fluid img-thumbnail"/>

      <p><b>[{{__('By using Desktop App')}}]</b></p>
      <p>{{__('From the telegram group click on the `three dots menu` and go to `Manage group`. Click on the `Members` option and click on the `Add Members` button. Search for the bot account. Select the bot account and click on Add button')}}</p>
      <img src="{{asset('assets/docs/images/admin_in_desktop.png')}}" class="img-fluid img-thumbnail"/>
      <p>{{__('Now, once again, navigate to the `Manage Group` options. From there, go to `Administrators` and click on the `Add Administrator` option. Choose the bot account and click on the `Save` button. That is it!')}}</p>
      <img src="{{asset('assets/docs/images/admin_in_desktop2.png')}}" class="img-fluid img-thumbnail"/>

      <p>{{__('Now, if you go to the Group Manager from the dashboard you will see the options for managing the groups.')}}</p>


      <div class="section-header">
        <h1 id="chat">{{ __("Filtering Message") }}</h1>
      </div>
      <hr />

      <p><b>{{__('Filter Members` Messages:')}}</b></p>
      <p>{{__(':appname advanced filtering system takes the burden off your shoulders by automatically screening messages from group members. With just a few clicks, you can:',['appname'=>config('app.name')])}}</p>

      <ol>
        <li><p><b>{{__('Remove Messages with Bot Commands:')}}</b> {{__('Keep your group chat clutter-free by eliminating messages that trigger bot commands.')}}</p></li>
        <li><p><b>{{__('Remove Messages Containing Images:')}}</b> {{__('Ensure focused discussions by removing images from the group.')}}</p></li>
        <li><p><b>{{__('Remove Messages Containing Voice Recordings:')}}</b> {{__('Optimize communication by eliminating voice messages.')}}</p></li>
        <li><p><b>{{__('Remove Messages Containing Attached Documents:')}}</b> {{__('Foster seamless conversations by filtering out messages with attached documents.')}}</p></li>
        <li><p><b>{{__('Remove Stickers and GIFs:')}}</b> {{__('Maintain a professional atmosphere by removing stickers and GIFs.')}}</p></li>
        <li><p><b>{{__('Remove Member Dice Rolls:')}}</b> {{__('Avoid spam and irrelevant messages by filtering out member dice rolls.')}}</p></li>
        <li><p><b>{{__('Remove Messages Contain Links:')}}</b> {{__('Prevent unauthorized links and maintain group security by removing messages containing URLs.')}}</p></li>
      </ol>
      <img src="{{asset('assets/docs/images/filter_member.png')}}" class="img-fluid img-thumbnail"/>


      <p><b>{{__('Filter Forwarded Messages:')}}</b></p>
      <p>{{__(':appname does not stop at member messages; it also efficiently handles forwarded messages. Enjoy a clean and focused group chat by:',['appname'=>config('app.name')])}}</p>

      <ol>
        <li><p><b>{{__('Remove Forwarded Messages with Media:')}}</b> {{__('Eliminate clutter by filtering out forwarded messages with media attachments.')}}</p></li>
        <li><p><b>{{__('Remove Forwarded Messages Contain Links:')}}</b> {{__('Enhance security by filtering forwarded messages that contain URLs.')}}</p></li>
        <li><p><b>{{__('Remove All Forwarded Messages:')}}</b> {{__('Streamline conversations by removing all forwarded messages for a more organized group experience.')}}</p></li>
      </ol>
      <img src="{{asset('assets/docs/images/filter_forworded.png')}}" class="img-fluid img-thumbnail"/>

      <p><b>{{__('Keyword Surveillance:')}}</b></p>
      <p>{{__('Your group`s reputation matters, and :appname has your back with its keyword surveillance feature. Automatically remove messages containing censor words to maintain a respectful and safe community.',['appname'=>config('app.name')])}}</p>
      <img src="{{asset('assets/docs/images/keyword_serv.png')}}" class="img-fluid img-thumbnail"/>

      <p><b>{{__('Service Message Control:')}}</b></p>
      <p>{{__('Keep your group announcements and updates tidy with :appname`s service message control:',['appname'=>config('app.name')])}}</p>
      <ol>
        <li><p><b>{{__('Delete `User Joined the Group` Message:')}}</b> {{__('Keep your announcements section clean by deleting user join messages.')}}</p></li>
        <li><p><b>{{__('Delete `User Left the Group` Message:')}}</b> {{__('Ensure a professional appearance by removing user leave messages.')}}</p></li>
      </ol>
      <img src="{{asset('assets/docs/images/service_manage.png')}}" class="img-fluid img-thumbnail"/>


      <p><b>{{__('New Members Restriction:')}}</b></p>
      <p>{{__(':appname allows you to set restrictions for new members joining your group. Decide how long a new member will be restricted, ensuring smooth onboarding and protection from potential spam or disruptive behavior.',['appname'=>config('app.name')])}}</p>
      <img src="{{asset('assets/docs/images/member_restriction.png')}}" class="img-fluid img-thumbnail"/>


      <p><b>{{__('Member message limitation:')}}</b></p>
      <ol>
        <li><p><b>{{__('Sending Same Message Will Be Deleted:')}}</b> {{__('Prevents message flooding and clutter in the group.')}}</p></li>
        <li><p><b>{{__('Sending Same Message Considered as Spam:')}}</b> {{__('Identifies and handles repetitive content to deter spamming behavior.')}}</p></li>
        <li><p><b>{{__('User Will Be Muted for Specific Time:')}}</b> {{__('Temporarily mutes users who violate message limitation rules to promote responsible behavior.')}}</p></li>
        <li><p><b>{{__('Control Over Message Frequency:')}}</b> {{__('Administrators have the flexibility to customize how frequently users can send messages in specific time frames.')}}</p></li>
      </ol>
      <img src="{{asset('assets/docs/images/member_limitation.png')}}" class="img-fluid img-thumbnail"/>


      <p>{{__('In conclusion, :appname offers a powerful suite of filtering and surveillance features to maintain a vibrant and secure Telegram group. By automating these tasks, you can focus on fostering engagement, building a strong community, and providing valuable content to your members. Elevate your Telegram group management with :appname and create a harmonious space for meaningful interactions.',['appname'=>config('app.name')])}}</p>

      <p>{{__('Take the first step towards a well-managed Telegram group today.')}}</p>


      <div class="section-header">
        <h1 id="subscriber">{{ __("Group Subscriber Manager") }}</h1>
      </div>
      <hr />
      <p>{{__('The mute and unmute feature in a Telegram group allows group administrators to manage subscribers` notification preferences effectively. Muting a subscriber ensures that they do not receive notifications for messages posted in the group, while unmuting restores normal notification behavior for the subscriber.')}}</p>
      <ul>
        <li>
          <p><b>{{__('Mute Subscriber:')}}</b></p>
          <p>{{__('Muting a subscriber in a Telegram group is useful when you want to temporarily prevent a specific member from receiving group notifications. This feature can be helpful in scenarios where a subscriber is causing disruptions, spamming, or violating group rules. Once muted, the subscriber will not receive any notification sounds, vibration alerts, or message banners for messages sent in the group.')}}</p>
          <p>{{__('When muting a subscriber, you can choose from various mute duration options, such as 1 hour, 8 hours, 2 days, 1 week, or custom duration.')}}</p>
        </li>
        <li>
          <p><b>{{__('Unmute Subscriber:')}}</b></p>
          <p>{{__('Unmuting a subscriber restores their notification settings to normal, allowing them to receive notifications for messages posted in the group.')}}</p>
        </li>
      </ul>

      <p>{{__('The ban and unban feature in a Telegram group allows group administrators to control membership by restricting or granting access to specific subscribers. Banning a subscriber removes them from the group and prevents them from rejoining or participating in the group`s discussions. On the other hand, unbanning a subscriber restores their membership privileges, allowing them to rejoin and participate in the group as before.')}}</p>
      <ul>
        <li>
          <p><b>{{__('Ban Subscriber:')}}</b></p>
          <p>{{__('Banning a subscriber is a powerful tool for group administrators to maintain order and enforce group rules. When a subscriber is banned, they are immediately removed from the group and lose all access to group content and discussions.')}}</p>
        </li>
        <li>
          <p><b>{{__('Unban Subscriber:')}}</b></p>
          <p>{{__('Unbanning a subscriber restores their membership rights and allows them to rejoin the group and participate in discussions as before.')}}</p>
        </li>
      </ul>

      <img src="{{asset('assets/docs/images/mute_ban.png')}}" class="img-fluid img-thumbnail"/>


      <div class="section-header">
        <h1 id="msg_campaign">{{ __("Message Campaign") }}</h1>
      </div>
      <hr />
      <p>{{__('With :appname, you can send message to a group instantly or schedule it for later. Some options in message sending by :appname are as follows',['appname'=>config('app.name')])}}</p>

      <ul>
        <li><b>{{__('Pin This Announcement:')}}</b> {{__('This option allows to highlight and keep important messages at the forefront of the group chat, ensuring that members easily access and stay informed about critical information.')}}</li>
        <li><b>{{__('Preview The URL In The Text Message:')}}</b> {{__('Enabling this feature automatically generates a preview of a linked webpage`s content within a text message, offering a convenient way for users to assess the linked content before clicking on the URL.')}}</li>
        <li><b>{{__('Protected Messages, No Copying Or Forwarding:')}}</b> {{__('It you enable this option during message sending then, no other member of that group will be able to copy or forword your message.')}}</li>
        <li><b>{{__('Sound Alerts For Messages Received By The Other Party:')}}</b> {{__('This is a feature that notifies users with a sound alert when the other party reads their messages in a messaging app, improving communication efficiency and providing real-time feedback on message status.')}}</li>
        <li><b>{{__('Delayed automatic deletion:')}}</b> {{__('This is a feature that allows users to set a time duration after which sent messages or media will be automatically deleted from the chat or conversation, promoting privacy and data security.')}}</li>
      </ul>

      <img src="{{asset('assets/docs/images/message_campaign.png')}}" class="img-fluid img-thumbnail"/>

  </section>
</div>
@endsection
