<?php

declare(strict_types=1);

namespace Dnklbgn\OAuth2\Client\Provider;

enum SpotifyScope: string
{
    /**
     * Read access to a user’s currently playing content
     */
    case UGS_IMAGE_UPLOAD = 'ugc-image-upload';

    /**
     * Read access to a user’s player state
     */
    case USER_READ_PLAYBACK_STATE = 'user-read-playback-state';

    /**
     * Write access to a user’s playback state
     */
    case USER_MODIFY_PLAYBACK_STATE = 'user-modify-playback-state';

    /**
     * Read access to a user’s currently playing content
     */
    case USER_READ_CURRENTLY_PLAYING = 'user-read-currently-playing';

    /**
     * Remote control playback of Spotify.
     * This scope is currently available to Spotify iOS and Android SDKs
     */
    case APP_REMOTE_CONTROL = 'app-remote-control';

    /**
     * Control playback of a Spotify track.
     * This scope is currently available to the Web Playback SDK.
     * The user must have a Spotify Premium account
     */
    case STREAMING = 'streaming';

    /**
     * Read access to user's private playlists
     */
    case PLAYLIST_READ_PRIVATE = 'playlist-read-private';

    /**
     * Include collaborative playlists when requesting a user's playlists
     */
    case PLAYLIST_READ_COLLABORATIVE = 'playlist-read-collaborative';

    /**
     * Write access to a user's private playlists
     */
    case PLAYLIST_MODIFY_PRIVATE = 'playlist-modify-private';

    /**
     * Write access to a user's public playlists
     */
    case PLAYLIST_MODIFY_PUBLIC = 'playlist-modify-public';

    /**
     * Write/delete access to the list of artists and other users that the user follows
     */
    case USER_FOLLOW_MODIFY = 'user-follow-modify';

    /**
     * Read access to the list of artists and other users that the user follows
     */
    case USER_FOLLOW_READ = 'user-follow-read';

    /**
     * Read access to a user’s playback position in a content
     */
    case USER_READ_PLAYBACK_POSITION = 'user-read-playback-position';

    /**
     * Read access to a user's top artists and tracks
     */
    case USER_TOP_READ = 'user-top-read';

    /**
     * Read access to a user’s recently played tracks
     */
    case USER_READ_RECENTLY_PLAYED = 'user-read-recently-played';

    /**
     * Write/delete access to a user's "Your Music" library
     */
    case USER_LIBRARY_MODIFY = 'user-library-modify';

    /**
     * Read access to a user's library
     */
    case USER_LIBRARY_READ = 'user-library-read';

    /**
     * Read access to user’s email address
     */
    case USER_READ_EMAIL = 'user-read-email';

    /**
     * Read access to user’s subscription details (type of user account)
     */
    case USER_READ_PRIVATE = 'user-read-private';

    /**
     * Link a partner user account to a Spotify user account
     */
    case USER_SOA_LINK = 'user-soa-link';

    /**
     * Unlink a partner user account from a Spotify account
     */
    case USER_SOA_UNLINK = 'user-soa-unlink';

    /**
     * Modify entitlements for linked users
     */
    case USER_MANAGE_ENTITLEMENTS = 'user-manage-entitlements';

    /**
     * Update partner information
     */
    case USER_MANAGE_PARTNER = 'user-manage-partner';

    /**
     * Create new partners, platform partners only
     */
    case USER_CREATE_PARTNER = 'user-create-partner';
}
