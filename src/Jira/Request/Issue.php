<?php

/**
 *
 */

namespace Jira\Request;

/**
 *
 */
class Issue
{

    /**
     *
     * @var string
     */
    protected $_issueKey;

    /**
     *
     * @var \Jira\Client
     */
    protected $_client;

    /**
     *
     */
    public function __construct(\Jira\Client $client, $issue_key)
    {
        $this->_client = $client;
        $this->_issueKey = $issue_key;
    }

    /**
     * Wrapper around \Jira\Client::call() that adds the issue key as the second
     * argument of the RPC call.
     *
     * @param string $method
     *   The method being invoked.
     * @param ...
     *   All additional arguments after the authentication token and issue key
     *   passed as the parameters to the RPC call.
     *
     * @return mixed
     *   The data returned by the RPC call.
     */
    public function call($method) {
        $args = func_get_args();
        $args[0] = $this->_issueKey;
        array_unshift($args, $method);
        return call_user_func_array(array($this->_client, 'call'), $args);
    }

    /**
     * Returns information about the issue.
     *
     * @see http://docs.atlassian.com/rpc-jira-plugin/latest/com/atlassian/jira/rpc/soap/JiraSoapService.html#getIssue(java.lang.String, java.lang.String)
     */
    public function get()
    {
        return $this->call('getIssue');
    }

    /**
     * Deletes the issue.
     *
     * @see http://docs.atlassian.com/rpc-jira-plugin/latest/com/atlassian/jira/rpc/soap/JiraSoapService.html#deleteIssue(java.lang.String, java.lang.String)
     */
    public function delete()
    {
        return $this->call('deleteIssue');
    }

    /**
     * Updates the issue with new values.
     *
     * @see http://docs.atlassian.com/rpc-jira-plugin/latest/com/atlassian/jira/rpc/soap/JiraSoapService.html#updateIssue(java.lang.String, java.lang.String, com.atlassian.jira.rpc.soap.beans.RemoteFieldValue[])
     */
    public function update()
    {
        return $this->_client->call('deleteIssue');
    }

    /**
     * Uploads an attachment to the issue with the specified issue key.
     *
     * @param string $filename
     *   The name of the attachment to be uploaded.
     * @param array $filedata
     *   A Base 64 encoded string of the attachment to be uploaded.
     *
     * @see Jira\Request\addAttachments()
     */
    public function addAttachment($filename, $filedata)
    {
        return $this->addAttachments(array($filename), array($filedata));
    }

    /**
     * Uploads an attachment to the issue with the specified issue key.
     *
     * @param array $filenames
     *   An array of filenames; each element names an attachment to be uploaded.
     * @param array $filedata
     *   An array of Base 64 encoded Strings; each element contains the data of
     *   the attachment to be uploaded
     *
     * @see http://docs.atlassian.com/rpc-jira-plugin/latest/com/atlassian/jira/rpc/soap/JiraSoapService.html#addBase64EncodedAttachmentsToIssue(java.lang.String, java.lang.String, java.lang.String[], java.lang.String[])
     */
    public function addAttachments(array $filenames, array $filedata)
    {
        return $this->call('addBase64EncodedAttachmentsToIssue', $filenames, $filedata);
    }

    /**
     * Adds a comment to the specified issue.
     *
     * @param \Jira\Object\Comment $comment
     *   The new comment to add.
     *
     * @see http://docs.atlassian.com/rpc-jira-plugin/latest/com/atlassian/jira/rpc/soap/JiraSoapService.html#addComment(java.lang.String, java.lang.String, com.atlassian.jira.rpc.soap.beans.RemoteComment)
     */
    public function addComment(\Jira\Object\Comment $comment)
    {
        return $this->call('addComment', $comment);
    }

    /**
     * Adds a worklog to the issue. The issue's time spent field will be increased
     * by the amount in \Jira\Object\Worklog::getTimeSpent().
     *
     * @param \Jira\Object\Worklog $worklog
     *   The worklog object.
     *
     * @see http://docs.atlassian.com/rpc-jira-plugin/latest/com/atlassian/jira/rpc/soap/JiraSoapService.html#addWorklogAndAutoAdjustRemainingEstimate(java.lang.String, java.lang.String, com.atlassian.jira.rpc.soap.beans.RemoteWorklog)
     */
    public function addWorklogAndAutoAdjustRemainingEstimate(\Jira\Object\Worklog $worklog)
    {
        return $this->call('addWorklogAndAutoAdjustRemainingEstimate', $worklog);
    }

    /**
     * Adds a worklog to the given issue but leaves the issue's remaining estimate
     * field unchanged. The issue's time spent field will be increased by the
     * amount in \Jira\Object\Worklog::getTimeSpent().
     *
     * @param \Jira\Object\Worklog $worklog
     *   The worklog object.
     *
     * @see http://docs.atlassian.com/rpc-jira-plugin/latest/com/atlassian/jira/rpc/soap/JiraSoapService.html#addWorklogAndRetainRemainingEstimate(java.lang.String, java.lang.String, com.atlassian.jira.rpc.soap.beans.RemoteWorklog)
     */
    public function addWorklogAndRetainRemainingEstimate(\Jira\Object\Worklog $worklog)
    {
        return $this->call('addWorklogAndRetainRemainingEstimate', $worklog);
    }

    /**
     * Adds a worklog to the given issue and sets the issue's remaining estimate
     * field to the given value. The issue's time spent field will be increased by
     * the amount in \Jira\Object\Worklog::getTimeSpent().
     *
     * @param \Jira\Object\Worklog $worklog
     *   The worklog object.
     * @param string $estimate
     *   The new value for the issue's remaining estimate as a duration string,
     *   e.g. "1d", "2h".
     *
     * @see http://docs.atlassian.com/rpc-jira-plugin/latest/com/atlassian/jira/rpc/soap/JiraSoapService.html#addWorklogWithNewRemainingEstimate(java.lang.String, java.lang.String, com.atlassian.jira.rpc.soap.beans.RemoteWorklog, java.lang.String)
     */
    public function addWorklogWithNewRemainingEstimate(\Jira\Object\Worklog $worklog, $estimate)
    {
        return $this->call('addWorklogWithNewRemainingEstimate', $worklog, $estimate);
    }

    /**
     * Creates an issue based on the passed details and makes it a child (e.g.
     * subtask) of this issue.
     *
     * @param \Jira\Object\Issue $issue
     *   The new issue to create.
     *
     * @see http://docs.atlassian.com/rpc-jira-plugin/latest/com/atlassian/jira/rpc/soap/JiraSoapService.html#createIssueWithParent(java.lang.String, com.atlassian.jira.rpc.soap.beans.RemoteIssue, java.lang.String)
     */
    public function createChildIssue(\Jira\Object\Issue $issue)
    {
        // We cannot use \Jira\Request\Issue::call() since the issue key is passed
        // as the last argument of the RPC call.
        return $this->_client->call($issue, $this->_issueKey);
    }

    /**
     * Creates an issue based on the passed details and security level and makes
     * it a child (e.g. subtask) of this issue.
     *
     * @param \Jira\Object\Issue $issue
     *   The new issue to create.
     * @param int $security_level
     *   The id of the required security level.
     *
     * @see http://docs.atlassian.com/rpc-jira-plugin/latest/com/atlassian/jira/rpc/soap/JiraSoapService.html#createIssueWithParentWithSecurityLevel(java.lang.String, com.atlassian.jira.rpc.soap.beans.RemoteIssue, java.lang.String, java.lang.Long)
     */
    public function createChildIssueWithSecurityLevel(\Jira\Object\Issue $issue, $security_level)
    {
        // We cannot use \Jira\Request\Issue::call() since the issue key is passed
        // as the third argument of the RPC call.
        return $this->_client->call($issue, $this->_issueKey, $security_level);
    }

    /**
     * Returns the attachments that are associated with this issue.
     *
     * @see http://docs.atlassian.com/rpc-jira-plugin/latest/com/atlassian/jira/rpc/soap/JiraSoapService.html#getAttachmentsFromIssue(java.lang.String, java.lang.String)
     */
    public function getAttachments()
    {
        return $this->call('getAttachmentsFromIssue');
    }

    /**
     * Returns the available actions that can be applied to this issue.
     *
     * @see http://docs.atlassian.com/rpc-jira-plugin/latest/com/atlassian/jira/rpc/soap/JiraSoapService.html#getAvailableActions(java.lang.String, java.lang.String)
     */
    public function getAvailableActions()
    {
        return $this->call('getAvailableActions');
    }

    /**
     * Gets the comments for this issue.
     *
     * @see http://docs.atlassian.com/rpc-jira-plugin/latest/com/atlassian/jira/rpc/soap/JiraSoapService.html#getComments(java.lang.String, java.lang.String)
     */
    public function getComments()
    {
        return $this->call('getComments');
    }

    /**
     * Returns the fields that are shown during an issue action.
     *
     * @param string $action_id
     *   The id of issue action to be executed.
     *
     * @see http://docs.atlassian.com/rpc-jira-plugin/latest/com/atlassian/jira/rpc/soap/JiraSoapService.html#getFieldsForAction(java.lang.String, java.lang.String, java.lang.String)
     */
    public function getFieldsForAction($action_id)
    {
        return $this->call('getComments', $action_id);
    }

    /**
     * Returns the fields that are shown when editing this issue.
     *
     * @see http://docs.atlassian.com/rpc-jira-plugin/latest/com/atlassian/jira/rpc/soap/JiraSoapService.html#getFieldsForEdit(java.lang.String, java.lang.String)
     */
    public function getFieldsForEdit()
    {
        return $this->call('getFieldsForEdit');
    }

    /**
     * Returns the resolution date for this issue. If the issue hasn't been
     * resolved yet, this method will return null.
     *
     * @see http://docs.atlassian.com/rpc-jira-plugin/latest/com/atlassian/jira/rpc/soap/JiraSoapService.html#getResolutionDateByKey(java.lang.String, java.lang.String)
     */
    public function getResolutionDate()
    {
        return $this->call('getResolutionDateByKey');
    }

    /**
     * Returns the current security level for this issue.
     *
     * @see http://docs.atlassian.com/rpc-jira-plugin/latest/com/atlassian/jira/rpc/soap/JiraSoapService.html#getSecurityLevel(java.lang.String, java.lang.String)
     */
    public function getSecurityLevel()
    {
        return $this->call('getSecurityLevel');
    }

    /**
     * Returns all worklogs for this issue.
     *
     * @see http://docs.atlassian.com/rpc-jira-plugin/latest/com/atlassian/jira/rpc/soap/JiraSoapService.html#getWorklogs(java.lang.String, java.lang.String)
     */
    public function getWorklogs()
    {
        return $this->call('getWorklogs');
    }

    /**
     * Determines if the logged in user has the permission to add worklogs to this
     * issue, that timetracking is enabled in JIRA, and that this issue is in an
     * editable workflow state.
     *
     * @see http://docs.atlassian.com/rpc-jira-plugin/latest/com/atlassian/jira/rpc/soap/JiraSoapService.html#hasPermissionToCreateWorklog(java.lang.String, java.lang.String)
     */
    public function hasPermissionToCreateWorklog()
    {
        return $this->call('hasPermissionToCreateWorklog');
    }

    /**
     * Progresses this issue through a workflow.
     *
     * @param string $action_id
     *   The id of the workflow action to progress to.
     * @param array $fields
     *   An array of \Jira\Object\Field objects that are changed in the workflow
     *   step.
     *
     * @see http://docs.atlassian.com/rpc-jira-plugin/latest/com/atlassian/jira/rpc/soap/JiraSoapService.html#progressWorkflowAction(java.lang.String, java.lang.String, java.lang.String, com.atlassian.jira.rpc.soap.beans.RemoteFieldValue[])
     */
    public function progressWorkflowAction($action_id, array $fields = array())
    {
        return $this->call('progressWorkflowAction', $action_id, $fields);
    }
}